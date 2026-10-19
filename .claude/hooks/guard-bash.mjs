// PreToolUse (Bash|PowerShell): bloquea comandos que pueden destruir datos (git push --force,
// artisan que escribe en la base) o exponer el .env. Solo mira comandos reales: ignora cuerpos de heredoc,
// here-strings y mensajes (git commit -m "...", echo "..."), para no dar falsos positivos.
import { block, readEvent } from './lib.mjs';

const event = await readEvent();
const raw = event.tool_input?.command;
if (!raw) process.exit(0);

// Sin el cuerpo de heredocs (<<'EOF' ... EOF) ni here-strings de PowerShell (@' ... '@).
const command = raw
  .replace(/<<-?\s*(['"]?)(\w+)\1[^\n]*\n[\s\S]*?\n[ \t]*\2[ \t]*(?=\n|$)/g, (m) => m.split('\n')[0])
  .replace(/@(['"])[ \t]*\n[\s\S]*?\n\1@/g, ' ');

// Separa por && || ; | y saltos de línea, pero solo fuera de comillas.
function splitSegments(text) {
  const out = [];
  let current = '';
  let quote = null;
  for (let i = 0; i < text.length; i++) {
    const ch = text[i];
    if (quote) {
      current += ch;
      if (ch === quote) quote = null;
    } else if (ch === '"' || ch === "'") {
      quote = ch;
      current += ch;
    } else if (ch === ';' || ch === '|' || ch === '\n' || (ch === '&' && text[i + 1] === '&')) {
      if (ch === '&' || (ch === '|' && text[i + 1] === '|')) i++;
      out.push(current);
      current = '';
    } else {
      current += ch;
    }
  }
  out.push(current);
  return out.map((s) => s.trim()).filter(Boolean);
}

const segments = splitSegments(command);

// .env y .env.* salvo .env.example, como argumento o destino de redirección.
const ENV_FILE = /(?<![\w.-])(?:[^\s'"|;&<>]*[\/\\])?\.env(?:\.(?!example(?![\w.-]))[\w.-]+)?(?![\w.-])/;
const QUOTED_ENV_PATH = /(?:^|[\/\\])\.env(?:\.(?!example(?![\w.-]))[\w.-]+)?$/;

const DESTRUCTIVE = new Set([
  'migrate', 'migrate:fresh', 'migrate:refresh', 'migrate:reset', 'migrate:rollback', 'db:wipe', 'db:seed',
]);
const testingOnSqlite = (seg) =>
  /--env[= ]testing\b/.test(seg) && /DB_CONNECTION['"]?\s*=\s*['"]?sqlite\b/.test(command);

for (const seg of segments) {
  const tokens = seg.split(/\s+/);

  // 1) git push
  if (tokens[0] === 'git') {
    let i = 1;
    while (i < tokens.length) {
      if (tokens[i] === '-C' || tokens[i] === '-c') i += 2;
      else if (tokens[i].startsWith('-')) i += 1;
      else break;
    }
    if (tokens[i] === 'push') {
      const args = tokens.slice(i + 1);
      const flags = args.filter((a) => a.startsWith('-'));
      const positional = args.filter((a) => !a.startsWith('-'));

      if (flags.some((f) => f === '-f' || f.startsWith('--force')) || positional.some((p) => p.startsWith('+'))) {
        block('Bloqueado: git push --force / -f / --force-with-lease. Hay datos reales y un workflow de backup; pedile al usuario que lo haga a mano si de verdad hace falta.');
      }
      // Push normal (incluido a main/master): permitido. Solo se bloquea el force.
    }
  }

  // 2) php artisan <comando que escribe en la base>. Solo si el comando ES php/artisan.
  const art = seg.match(/^(?:[A-Za-z_]\w*=\S*\s+)*(?:&\s*)?("[^"]*"|'[^']*'|\S+)\s+(?:-\S+\s+)*artisan\s+([\w:.-]+)/);
  if (art && (/php(\.exe)?["']?$/i.test(art[1]) || /^\$\w+$/.test(art[1])) && DESTRUCTIVE.has(art[2])) {
    if (!testingOnSqlite(seg)) {
      block(`Bloqueado: php artisan ${art[2]} escribe en la base y puede apuntar a datos reales (producción). Para probar usá php artisan test; si hace falta el comando, forzá sqlite: DB_CONNECTION=sqlite DB_DATABASE=:memory: php artisan ${art[2]} --env=testing.`);
    }
  }

  // 3) .env: ni leerlo ni escribirlo desde la shell (cat, Get-Content, >, tee, cp, sed -i...).
  const unquoted = seg.replace(/"[^"]*"|'[^']*'/g, ' ');
  const quotedPaths = (seg.match(/"[^"]*"|'[^']*'/g) || []).map((q) => q.slice(1, -1));
  if (ENV_FILE.test(unquoted) || quotedPaths.some((q) => QUOTED_ENV_PATH.test(q))) {
    block('Bloqueado: el .env tiene credenciales reales (Mercado Pago, mailers, S3, Sentry, base) y no se lee ni se modifica desde Claude. Para ver qué variables existen usá .env.example; los cambios de .env los hace el usuario.');
  }
}

process.exit(0);
