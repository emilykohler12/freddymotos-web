// PostToolUse (Edit|Write|MultiEdit): php -l y Pint sobre el .php editado.
import { existsSync } from 'node:fs';
import { spawnSync } from 'node:child_process';
import path from 'node:path';
import { block, findPhp, projectRoot, readEvent, relativeToRoot } from './lib.mjs';

const event = await readEvent();
const file = event.tool_input?.file_path;
if (!file || !file.endsWith('.php')) process.exit(0);

const root = projectRoot(event);
const rel = relativeToRoot(root, file);
const abs = path.resolve(root, file);
if (!rel || rel.startsWith('vendor/') || !existsSync(abs)) process.exit(0);

const php = findPhp();
if (!php) {
  process.stderr.write('php-check: no encontré php (ni en PATH ni en Laragon); salteo la verificación.\n');
  process.exit(0);
}

const lint = spawnSync(php, ['-l', abs], { encoding: 'utf8', timeout: 15000 });
if (lint.status !== 0) {
  block(`Error de sintaxis PHP en ${rel}:\n${(lint.stdout + lint.stderr).trim()}`);
}

// Pint corrige el estilo del archivo (puede reescribirlo). Si falla, avisa.
const pint = path.join(root, 'vendor', 'bin', 'pint');
if (existsSync(pint)) {
  const r = spawnSync(php, [pint, abs], { cwd: root, encoding: 'utf8', timeout: 20000 });
  if (r.status !== 0 && r.status !== null) {
    block(`php-check: Pint no pudo formatear ${rel}:\n${(r.stdout + r.stderr).trim()}`);
  }
}

process.exit(0);
