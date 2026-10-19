// PreToolUse (Bash): bloquea comandos que pueden destruir datos o la rama principal.
import { spawnSync } from 'node:child_process';
import { block, projectRoot, readEvent } from './lib.mjs';

const event = await readEvent();
const command = event.tool_input?.command;
if (!command) process.exit(0);

const root = projectRoot(event);

// 1) git push (cada segmento del comando por separado)
for (const segment of command.split(/&&|\|\||[;|\n]/)) {
  const tokens = segment.trim().split(/\s+/);
  if (tokens[0] !== 'git') continue;
  const pushAt = tokens.indexOf('push');
  if (pushAt === -1) continue;

  const args = tokens.slice(pushAt + 1);
  const flags = args.filter((a) => a.startsWith('-'));
  const positional = args.filter((a) => !a.startsWith('-'));

  if (flags.some((f) => f === '-f' || f.startsWith('--force')) || positional.some((p) => p.startsWith('+'))) {
    block('Bloqueado: git push --force / -f / --force-with-lease. Hay datos reales y un workflow de backup; pedile al usuario que lo haga a mano si de verdad hace falta.');
  }
  if (positional.some((p) => /(^|[:/])(main|master)$/.test(p))) {
    block('Bloqueado: git push a main/master. Pusheá una rama de trabajo y abrí un PR.');
  }
  // `git push` / `git push origin` sin rama explícita: empuja la rama actual.
  if (positional.length <= 1) {
    const br = spawnSync('git', ['branch', '--show-current'], { cwd: root, encoding: 'utf8' });
    if (['main', 'master'].includes(br.stdout?.trim())) {
      block('Bloqueado: estás en main/master y git push sin rama explícita empujaría a main. Cambiá a una rama de trabajo.');
    }
  }
}

// 2) comandos artisan destructivos (salvo contra sqlite de pruebas)
const destructive = /\bartisan\s+(migrate:fresh|migrate:refresh|migrate:reset|migrate:rollback|db:wipe)\b/;
const hit = command.match(destructive);
if (hit) {
  const safe = /--env[= ]testing\b/.test(command) && /DB_CONNECTION=sqlite\b/.test(command);
  if (!safe) {
    block(`Bloqueado: php artisan ${hit[1]} puede borrar datos reales. Para probar usá el entorno de tests (php artisan test) o, si hace falta el comando, forzá sqlite: DB_CONNECTION=sqlite DB_DATABASE=:memory: php artisan ${hit[1]} --env=testing.`);
  }
}

process.exit(0);
