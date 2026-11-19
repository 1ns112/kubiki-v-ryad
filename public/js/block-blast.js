(() => {
    'use strict';

    const SIZE = 8;
    const COLORS = ['#f87171', '#fb923c', '#facc15', '#4ade80', '#38bdf8', '#a78bfa', '#f472b6'];

    const SHAPES = [
        ['X'],
        ['XX'], ['X', 'X'],
        ['XXX'], ['X', 'X', 'X'],
        ['XXXX'], ['X', 'X', 'X', 'X'],
        ['XXXXX'], ['X', 'X', 'X', 'X', 'X'],
        ['XX', 'XX'],
        ['XXX', 'XXX', 'XXX'],
        ['XX', 'X.'], ['XX', '.X'], ['X.', 'XX'], ['.X', 'XX'],
        ['XXX', 'X..'], ['XXX', '..X'], ['X..', 'XXX'], ['..X', 'XXX'],
        ['X.', 'X.', 'XX'], ['.X', '.X', 'XX'], ['XX', 'X.', 'X.'], ['XX', '.X', '.X'],
        ['XXX', '.X.'], ['.X.', 'XXX'],
        ['XX.', '.XX'], ['.XX', 'XX.'],
    ];

    const boardEl = document.getElementById('board');
    const trayEl = document.getElementById('tray');
    const scoreEl = document.getElementById('score');
    const bestEl = document.getElementById('best');
    const overlayEl = document.getElementById('overlay');
    const finalScoreEl = document.getElementById('finalScore');
    const toastEl = document.getElementById('toast');

    const PHRASES = ['Хорошо получается', 'Так держать', 'Гениально'];

    let board = [];
    let pieces = [];
    let selected = -1;
    let score = 0;
    let best = Math.max(
        Number(localStorage.getItem('blockBlastBest')) || 0,
        Number(window.APP_BEST) || 0
    );
    let over = false;
    let hover = null;
    let cellEls = [];
    let streak = 0;
    let phraseTimer;

    const parse = (rows) => rows.flatMap((row, r) =>
        [...row].map((ch, c) => (ch === 'X' ? [r, c] : null)).filter(Boolean)
    );

    function newPiece() {
        const shape = SHAPES[Math.floor(Math.random() * SHAPES.length)];
        return {
            cells: parse(shape),
            h: shape.length,
            w: Math.max(...shape.map((row) => row.length)),
            color: COLORS[Math.floor(Math.random() * COLORS.length)],
        };
    }

    function deal() {
        return [newPiece(), newPiece(), newPiece()];
    }

    function buildBoard() {
        boardEl.innerHTML = '';
        cellEls = [];
        for (let r = 0; r < SIZE; r++) {
            for (let c = 0; c < SIZE; c++) {
                const cell = document.createElement('button');
                cell.type = 'button';
                cell.className = 'cell';
                cell.setAttribute('aria-label', `Клетка ${r + 1}, ${c + 1}`);
                cell.innerHTML = '<span class="block"></span>';
                cell.addEventListener('mouseenter', () => { hover = [r, c]; renderPreview(); });
                cell.addEventListener('mouseleave', () => { hover = null; renderPreview(); });
                cell.addEventListener('focus', () => { hover = [r, c]; renderPreview(); });
                cell.addEventListener('blur', () => { hover = null; renderPreview(); });
                cell.addEventListener('click', () => tryPlace(r, c));
                boardEl.appendChild(cell);
                cellEls.push(cell);
            }
        }
    }

    function origin(piece, r, c) {
        const or = Math.min(Math.max(r - Math.floor((piece.h - 1) / 2), 0), SIZE - piece.h);
        const oc = Math.min(Math.max(c - Math.floor((piece.w - 1) / 2), 0), SIZE - piece.w);
        return [or, oc];
    }

    function canPlace(piece, or, oc) {
        return piece.cells.every(([dr, dc]) => {
            const r = or + dr;
            const c = oc + dc;
            return r >= 0 && c >= 0 && r < SIZE && c < SIZE && !board[r][c];
        });
    }

    function existsPlacement(piece) {
        for (let or = 0; or <= SIZE - piece.h; or++) {
            for (let oc = 0; oc <= SIZE - piece.w; oc++) {
                if (canPlace(piece, or, oc)) return true;
            }
        }
        return false;
    }

    function hasMove() {
        return pieces.some((piece) => piece && existsPlacement(piece));
    }

    function renderBoard() {
        for (let r = 0; r < SIZE; r++) {
            for (let c = 0; c < SIZE; c++) {
                const cell = cellEls[r * SIZE + c];
                const value = board[r][c];
                cell.classList.toggle('filled', !!value);
                cell.classList.remove('ghost');
                if (value) cell.style.setProperty('--c', value);
                else cell.style.removeProperty('--c');
            }
        }
    }

    function renderTray() {
        trayEl.innerHTML = '';
        pieces.forEach((piece, i) => {
            const slot = document.createElement('button');
            slot.type = 'button';
            slot.className = 'slot' + (i === selected ? ' selected' : '');
            slot.disabled = !piece;
            slot.setAttribute('aria-label', piece ? `Фигура ${i + 1}` : 'Пустой слот');

            if (piece) {
                const size = Math.min(20, 76 / Math.max(piece.w, piece.h));
                const grid = document.createElement('div');
                grid.className = 'mini';
                grid.style.gridTemplateColumns = `repeat(${piece.w}, ${size}px)`;
                grid.style.gridAutoRows = `${size}px`;
                const filled = new Set(piece.cells.map(([r, c]) => r * 100 + c));

                for (let r = 0; r < piece.h; r++) {
                    for (let c = 0; c < piece.w; c++) {
                        const cell = document.createElement('div');
                        cell.className = 'mini-cell' + (filled.has(r * 100 + c) ? ' on' : '');
                        if (filled.has(r * 100 + c)) cell.style.setProperty('--c', piece.color);
                        grid.appendChild(cell);
                    }
                }
                slot.appendChild(grid);
                slot.addEventListener('click', () => {
                    selected = selected === i ? -1 : i;
                    renderTray();
                    renderPreview();
                });
            }

            trayEl.appendChild(slot);
        });
    }

    function renderPreview() {
        cellEls.forEach((cell) => cell.classList.remove('ghost'));
        if (selected < 0 || !hover) return;

        const piece = pieces[selected];
        if (!piece) return;

        const [or, oc] = origin(piece, hover[0], hover[1]);
        if (!canPlace(piece, or, oc)) return;

        piece.cells.forEach(([dr, dc]) => {
            const cell = cellEls[(or + dr) * SIZE + (oc + dc)];
            cell.classList.add('ghost');
            cell.style.setProperty('--c', piece.color);
        });
    }

    function renderAll() {
        renderBoard();
        renderTray();
        renderPreview();
        scoreEl.textContent = score;
        bestEl.textContent = best;
    }

    function bump(el) {
        el.classList.remove('bump');
        void el.offsetWidth;
        el.classList.add('bump');
    }

    function clearLines() {
        const rows = [];
        const cols = [];

        for (let r = 0; r < SIZE; r++) {
            if (board[r].every(Boolean)) rows.push(r);
        }
        for (let c = 0; c < SIZE; c++) {
            if (board.every((row) => row[c])) cols.push(c);
        }

        const count = rows.length + cols.length;
        if (!count) return 0;

        const cleared = new Set();
        rows.forEach((r) => { for (let c = 0; c < SIZE; c++) cleared.add(r * SIZE + c); });
        cols.forEach((c) => { for (let r = 0; r < SIZE; r++) cleared.add(r * SIZE + c); });

        cleared.forEach((key) => {
            board[Math.floor(key / SIZE)][key % SIZE] = null;
            const cell = cellEls[key];
            cell.classList.add('flash');
            setTimeout(() => cell.classList.remove('flash'), 400);
        });

        score += count * 10;
        return count;
    }

    function showPhrase(streak) {
        toastEl.textContent = PHRASES[Math.min(streak - 2, PHRASES.length - 1)];
        toastEl.classList.remove('show');
        void toastEl.offsetWidth;
        toastEl.classList.add('show');
        clearTimeout(phraseTimer);
        phraseTimer = setTimeout(() => toastEl.classList.remove('show'), 1600);
    }

    function saveScore() {
        if (!window.APP_AUTH) return;

        fetch('/scores', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                Accept: 'application/json',
            },
            body: JSON.stringify({ score }),
        })
            .then((response) => (response.ok ? response.json() : null))
            .then((data) => {
                if (data && data.best > best) {
                    best = data.best;
                    localStorage.setItem('blockBlastBest', best);
                    bestEl.textContent = best;
                }
            })
            .catch(() => {});
    }

    function tryPlace(r, c) {
        if (over || selected < 0) return;

        const piece = pieces[selected];
        if (!piece) return;

        const [or, oc] = origin(piece, r, c);
        if (!canPlace(piece, or, oc)) return;

        piece.cells.forEach(([dr, dc]) => {
            board[or + dr][oc + dc] = piece.color;
        });

        pieces[selected] = null;
        selected = -1;
        score += piece.cells.length;

        if (clearLines()) {
            streak++;
            if (streak >= 2) showPhrase(streak);
        } else {
            streak = 0;
        }

        if (pieces.every((p) => !p)) pieces = deal();

        if (!hasMove()) {
            over = true;
            if (score > best) {
                best = score;
                localStorage.setItem('blockBlastBest', best);
            }
            finalScoreEl.textContent = score;
            overlayEl.classList.remove('hidden');
            saveScore();
        }

        hover = null;
        renderAll();
        bump(scoreEl);
    }

    function reset() {
        board = Array.from({ length: SIZE }, () => Array(SIZE).fill(null));
        pieces = deal();
        selected = -1;
        score = 0;
        over = false;
        hover = null;
        streak = 0;
        clearTimeout(phraseTimer);
        toastEl.classList.remove('show');
        overlayEl.classList.add('hidden');
        renderAll();
    }

    document.getElementById('restart').addEventListener('click', reset);

    bestEl.textContent = best;
    buildBoard();
    reset();
})();
