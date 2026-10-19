// Utilidades compartidas por los hooks de Claude Code (Node, sin dependencias).
import { existsSync, readdirSync } from 'node:fs';
import { spawnSync } from 'node:child_process';
import path from 'node:path';

export function readEvent() {
  return new Promise((resolve) => {
    let raw = '';
    process.stdin.setEncoding('utf8');
    process.stdin.on('data', (c) => (raw += c));
    process.stdin.on('end', () => {
      try {
        resolve(JSON.parse(raw || '{}'));
      } catch {
        resolve({});
      }
    });
  });
}

// exit 2 = bloquear; el mensaje de stderr le llega a Claude.
export function block(message) {
  process.stderr.write(`${message}\n`);
  process.exit(2);
}

export function projectRoot(event) {
  return path.resolve(process.env.CLAUDE_PROJECT_DIR || event.cwd || process.cwd());
}

// Ruta relativa al proyecto con "/" (vacía si el archivo queda fuera del proyecto).
export function relativeToRoot(root, file) {
  const abs = path.resolve(root, file);
  const rel = path.relative(root, abs);
  if (rel.startsWith('..') || path.isAbsolute(rel)) return '';
  return rel.split(path.sep).join('/');
}

// `php` del PATH, o el más nuevo de Laragon (C:\laragon\bin\php\php-*\php.exe).
export function findPhp() {
  if (spawnSync('php', ['-v'], { stdio: 'ignore' }).status === 0) return 'php';
  const base = 'C:\\laragon\\bin\\php';
  if (!existsSync(base)) return null;
  const dirs = readdirSync(base).filter((d) => d.startsWith('php')).sort().reverse();
  for (const d of dirs) {
    const exe = path.join(base, d, 'php.exe');
    if (existsSync(exe)) return exe;
  }
  return null;
}
