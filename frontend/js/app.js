const API_URL = 'http://localhost:8000';

const editor          = document.getElementById('code-editor');
const lineNumbers     = document.getElementById('line-numbers');
const editorLines     = document.getElementById('editor-lines');
const consoleEl       = document.getElementById('console');
const loader          = document.getElementById('loader');
const toast           = document.getElementById('toast');
const btnNew          = document.getElementById('btn-new');
const btnLoad         = document.getElementById('btn-load');
const btnSave         = document.getElementById('btn-save');
const btnRun          = document.getElementById('btn-run');
const btnClearConsole = document.getElementById('btn-clear-console');
const fileInput       = document.getElementById('file-input');
const btnDlErrors     = document.getElementById('btn-dl-errors');
const btnDlSymbols    = document.getElementById('btn-dl-symbols');
const btnDlOutput     = document.getElementById('btn-dl-output');
const badgeErrors     = document.getElementById('badge-errors');
const badgeSymbols    = document.getElementById('badge-symbols');
const errorsPlaceholder  = document.getElementById('errors-placeholder');
const symbolsPlaceholder = document.getElementById('symbols-placeholder');
const errorsTable    = document.getElementById('errors-table');
const symbolsTable   = document.getElementById('symbols-table');
const errorsTbody    = document.getElementById('errors-tbody');
const symbolsTbody   = document.getElementById('symbols-tbody');

let lastResult = null;

// ── Line numbers ──────────────────────────────────────────────────────
function updateLineNumbers() {
  const lines = editor.value.split('\n');
  editorLines.textContent = `${lines.length} línea${lines.length !== 1 ? 's' : ''}`;
  lineNumbers.innerHTML = lines.map((_, i) => `<span>${i + 1}</span>`).join('');
}
editor.addEventListener('input', updateLineNumbers);
editor.addEventListener('scroll', () => { lineNumbers.scrollTop = editor.scrollTop; });
editor.addEventListener('keydown', e => {
  if (e.key === 'Tab') {
    e.preventDefault();
    const s = editor.selectionStart;
    editor.value = editor.value.slice(0, s) + '    ' + editor.value.slice(editor.selectionEnd);
    editor.selectionStart = editor.selectionEnd = s + 4;
    updateLineNumbers();
  }
});
updateLineNumbers();

// ── View switching ────────────────────────────────────────────────────
function switchView(name) {
  document.querySelectorAll('.view-tab').forEach(t => t.classList.toggle('view-tab--active', t.dataset.view === name));
  document.querySelectorAll('.right-view').forEach(v => v.classList.toggle('right-view--active', v.id === 'view-' + name));
}
document.querySelectorAll('.view-tab').forEach(tab => {
  tab.addEventListener('click', () => switchView(tab.dataset.view));
});

// ── Console ───────────────────────────────────────────────────────────
function clearConsole() {
  consoleEl.innerHTML = '<span class="console__placeholder">// La salida del intérprete aparecerá aquí...</span>';
}
function printConsole(text, type = 'out') {
  consoleEl.innerHTML = '';
  if (!text?.trim()) { clearConsole(); return; }
  text.split('\n').forEach(line => {
    const s = document.createElement('span');
    s.className = `line-${type}`; s.textContent = line;
    consoleEl.appendChild(s);
    consoleEl.appendChild(document.createElement('br'));
  });
  consoleEl.scrollTop = consoleEl.scrollHeight;
}
function appendConsole(text, type = 'out') {
  consoleEl.querySelector('.console__placeholder')?.remove();
  text.split('\n').forEach((line, i, arr) => {
    const s = document.createElement('span');
    s.className = `line-${type}`; s.textContent = line;
    consoleEl.appendChild(s);
    if (i < arr.length - 1) consoleEl.appendChild(document.createElement('br'));
  });
  consoleEl.scrollTop = consoleEl.scrollHeight;
}

// ── Toast ─────────────────────────────────────────────────────────────
function showToast(msg, type = 'info') {
  toast.textContent = msg;
  toast.className = `toast toast--${type} toast--show`;
  setTimeout(() => { toast.className = 'toast'; }, 2800);
}

// ── Render reports ────────────────────────────────────────────────────
function renderErrors(errors) {
  const n = errors.length;
  badgeErrors.textContent = n;
  badgeErrors.className = 'view-tab__badge' + (n > 0 ? ' view-tab__badge--err' : ' view-tab__badge--ok');

  if (n === 0) {
    errorsTable.hidden = true;
    errorsPlaceholder.hidden = false;
    errorsPlaceholder.innerHTML = '<span style="color:var(--green)">✓ Sin errores detectados.</span>';
    return;
  }
  errorsPlaceholder.hidden = true;
  errorsTable.hidden = false;
  errorsTbody.innerHTML = errors.map(e => {
    const tipo = e.tipo ?? e.type ?? '';
    const desc = e.descripcion ?? e.description ?? '';
    const lin  = e.linea ?? e.line ?? '-';
    const col  = e.columna ?? e.column ?? '-';
    const cls  = tipo.toLowerCase().includes('léx') || tipo.toLowerCase().includes('lex') ? 'cell-lex'
               : tipo.toLowerCase().includes('sin') ? 'cell-sin'
               : tipo.toLowerCase().includes('sem') ? 'cell-sem' : 'cell-int';
    return `<tr><td class="${cls}">${esc(tipo)}</td><td>${esc(desc)}</td><td>${lin}</td><td>${col}</td></tr>`;
  }).join('');
}

function renderSymbols(symbols) {
  const n = symbols.length;
  badgeSymbols.textContent = n;
  badgeSymbols.className = 'view-tab__badge' + (n > 0 ? ' view-tab__badge--ok' : '');

  if (n === 0) {
    symbolsTable.hidden = true;
    symbolsPlaceholder.hidden = false;
    return;
  }
  symbolsPlaceholder.hidden = true;
  symbolsTable.hidden = false;
  symbolsTbody.innerHTML = symbols.map(s => {
    const name  = s.name  ?? s.nombre ?? '';
    const type  = s.type  ?? s.tipo   ?? '';
    const kind  = s.kind  ?? s.clase  ?? '';
    const scope = s.scope ?? 'global';
    const value = s.value ?? 'nil';
    const line  = s.line  ?? '-';
    const cls   = kind === 'var' ? 'cell-var' : kind === 'const' ? 'cell-const' : kind === 'function' ? 'cell-func' : '';
    return `<tr><td>${esc(name)}</td><td>${esc(type)}</td><td class="${cls}">${esc(kind)}</td><td>${esc(String(value))}</td><td>${esc(scope)}</td><td>${line}</td></tr>`;
  }).join('');
}

function resetReports() {
  badgeErrors.textContent  = '0'; badgeErrors.className  = 'view-tab__badge';
  badgeSymbols.textContent = '0'; badgeSymbols.className = 'view-tab__badge';
  errorsTable.hidden = true;  errorsPlaceholder.hidden  = false;
  errorsPlaceholder.innerHTML = '<span>// Ejecuta el código para ver los errores.</span>';
  symbolsTable.hidden = true; symbolsPlaceholder.hidden = false;
  symbolsPlaceholder.innerHTML = '<span>// Ejecuta el código para ver la tabla de símbolos.</span>';
  [btnDlErrors, btnDlSymbols, btnDlOutput].forEach(b => b.disabled = true);
  lastResult = null;
}

// ── Downloads ─────────────────────────────────────────────────────────
function esc(s) { return String(s).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;'); }
function dl(name, content, type = 'text/plain') {
  const a = document.createElement('a');
  a.href = URL.createObjectURL(new Blob([content], { type }));
  a.download = name; a.click(); URL.revokeObjectURL(a.href);
}
function errorsHtml(errors) {
  const rows = errors.map(e => `<tr><td>${esc(e.tipo??'')}</td><td>${esc(e.descripcion??'')}</td><td>${e.linea??'-'}</td><td>${e.columna??'-'}</td></tr>`).join('');
  return `<!DOCTYPE html><html lang="es"><head><meta charset="UTF-8"/><title>Errores Golampi</title>
<style>body{font-family:monospace;background:#0a0c0f;color:#e2e8f0;padding:32px}h1{color:#00e87a;margin-bottom:20px}
table{width:100%;border-collapse:collapse}th{background:#0f1115;color:#7a8599;padding:8px 14px;text-align:left;border-bottom:1px solid #1f2430;font-size:11px}
td{padding:8px 14px;border-bottom:1px solid #1a1e26}tr:hover td{background:#13161b}</style></head>
<body><h1>⬡ ERRORES — GOLAMPI</h1><table><thead><tr><th>TIPO</th><th>DESCRIPCIÓN</th><th>LÍNEA</th><th>COL</th></tr></thead>
<tbody>${rows||'<tr><td colspan="4" style="color:#3d4558;font-style:italic">Sin errores.</td></tr>'}</tbody></table></body></html>`;
}
function symbolsHtml(symbols) {
  const rows = symbols.map(s => `<tr><td>${esc(s.name??'')}</td><td>${esc(s.type??'')}</td><td>${esc(s.kind??'')}</td><td>${esc(String(s.value??'nil'))}</td><td>${esc(s.scope??'global')}</td><td>${s.line??'-'}</td></tr>`).join('');
  return `<!DOCTYPE html><html lang="es"><head><meta charset="UTF-8"/><title>Símbolos Golampi</title>
<style>body{font-family:monospace;background:#0a0c0f;color:#e2e8f0;padding:32px}h1{color:#00e87a;margin-bottom:20px}
table{width:100%;border-collapse:collapse}th{background:#0f1115;color:#7a8599;padding:8px 14px;text-align:left;border-bottom:1px solid #1f2430;font-size:11px}
td{padding:8px 14px;border-bottom:1px solid #1a1e26}tr:hover td{background:#13161b}</style></head>
<body><h1>⬡ TABLA DE SÍMBOLOS — GOLAMPI</h1><table><thead><tr><th>NOMBRE</th><th>TIPO</th><th>CLASE</th><th>VALOR</th><th>ÁMBITO</th><th>LÍNEA</th></tr></thead>
<tbody>${rows||'<tr><td colspan="6" style="color:#3d4558;font-style:italic">Sin símbolos.</td></tr>'}</tbody></table></body></html>`;
}

btnDlErrors.addEventListener('click',  () => { if (lastResult) { dl('errores.html',  errorsHtml(lastResult.errors??[]),   'text/html'); showToast('Errores exportados','ok'); }});
btnDlSymbols.addEventListener('click', () => { if (lastResult) { dl('simbolos.html', symbolsHtml(lastResult.symbols??[]), 'text/html'); showToast('Símbolos exportados','ok'); }});
btnDlOutput.addEventListener('click',  () => { if (lastResult) { dl('salida.txt', lastResult.output??''); showToast('Salida exportada','ok'); }});

// ── Toolbar ───────────────────────────────────────────────────────────
btnNew.addEventListener('click', () => {
  if (editor.value.trim() && !confirm('¿Limpiar editor y consola?')) return;
  editor.value = ''; updateLineNumbers();
  clearConsole(); resetReports();
  switchView('console');
  showToast('Entorno limpiado', 'info');
});
btnLoad.addEventListener('click', () => fileInput.click());
fileInput.addEventListener('change', () => {
  const f = fileInput.files[0]; if (!f) return;
  const fr = new FileReader();
  fr.onload = e => { editor.value = e.target.result; updateLineNumbers(); showToast(`Cargado: ${f.name}`, 'info'); };
  fr.readAsText(f); fileInput.value = '';
});
btnSave.addEventListener('click', () => {
  if (!editor.value.trim()) { showToast('Editor vacío', 'err'); return; }
  dl('programa.golampi', editor.value); showToast('Guardado', 'ok');
});
btnClearConsole.addEventListener('click', () => { clearConsole(); switchView('console'); });

// ── Run ───────────────────────────────────────────────────────────────
btnRun.addEventListener('click', async () => {
  const code = editor.value.trim();
  if (!code) { showToast('Editor vacío', 'err'); return; }

  loader.hidden = false; btnRun.disabled = true;
  clearConsole(); resetReports(); switchView('console');

  try {
    const resp = await fetch(API_URL, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ code }),
    });
    if (!resp.ok) throw new Error(`HTTP ${resp.status}`);
    const result = await resp.json();
    lastResult = result;

    // Consola
    if (result.output?.trim()) {
      printConsole(result.output, 'out');
    }
    if (result.errors?.length) {
      if (result.output?.trim()) appendConsole('', 'info');
      else consoleEl.innerHTML = '';
      appendConsole(`─── ${result.errors.length} error(es) detectado(s) ───`, 'err');
      result.errors.forEach(e => {
        const loc = (e.linea??e.line) ? ` [L${e.linea??e.line}:C${e.columna??e.column??'?'}]` : '';
        appendConsole(`  ${e.tipo??'Error'}: ${e.descripcion??''}${loc}`, 'err');
      });
    }
    if (!result.output?.trim() && !result.errors?.length) {
      appendConsole('// Ejecución completada sin salida.', 'info');
    }

    renderErrors(result.errors ?? []);
    renderSymbols(result.symbols ?? []);
    [btnDlErrors, btnDlSymbols, btnDlOutput].forEach(b => b.disabled = false);

    result.success ? showToast('Compilación exitosa', 'ok') : showToast(`${result.errors?.length ?? 0} error(es)`, 'err');

  } catch (err) {
    clearConsole();
    appendConsole('Error de conexión con el servidor backend.', 'err');
    appendConsole(`Servidor: ${API_URL}`, 'info');
    appendConsole(`Detalle: ${err.message}`, 'err');
    showToast('Error de conexión', 'err');
  } finally {
    loader.hidden = true; btnRun.disabled = false;
  }
});

document.addEventListener('keydown', e => {
  if ((e.ctrlKey || e.metaKey) && e.key === 'Enter') { e.preventDefault(); btnRun.click(); }
});