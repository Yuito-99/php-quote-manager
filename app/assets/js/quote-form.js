/*
 * assets/js/quote-form.js
 * 見積フォーム(create.php / edit.php)共通のJavaScript
 *
 * 前提: 呼び出し元のHTMLには以下が存在すること
 *   - class="quotes-form" のフォーム
 *   - id="quote-items-body" の明細行 <tbody>
 *   - id="btn-add-row" の行追加ボタン
 *   - 各行に class="btn-remove-row" の削除ボタン
 *
 * 初期の itemIndex(次に追加する行の添字)は、
 * 呼び出し元のHTML側で `window.initialItemIndex` に設定してから、
 * このファイルを読み込むこと(未設定の場合は 1 を初期値とする)。
 *
 * confirmMessageOnSubmit を設定した場合、送信時に確認ダイアログを出す。
 * (create.php では未設定のまま、edit.php では
 *  `window.confirmMessageOnSubmit = 'この内容で更新しますか？';` のように設定して使う想定)
 */

document.addEventListener('DOMContentLoaded', function () {
    const form = document.querySelector('.quotes-form');
    const tbody = document.getElementById('quote-items-body');
    const addButton = document.getElementById('btn-add-row');

    if (!form || !tbody || !addButton) {
        return; // 想定するフォーム構造が無いページでは何もしない
    }

    let itemIndex = window.initialItemIndex || 1;

    // 行を追加
    addButton.addEventListener('click', function () {
        const firstRow = tbody.querySelector('tr');
        const newRow = firstRow.cloneNode(true);

        newRow.querySelectorAll('input').forEach(function (input) {
            input.name = input.name.replace(/items\[\d+\]/, 'items[' + itemIndex + ']');
            if (input.type === 'number') {
                input.value = input.name.includes('quantity') ? '1' : '0';
            } else {
                input.value = '';
            }
        });

        tbody.appendChild(newRow);
        itemIndex++;
    });

    // 削除ボタン(イベント委譲: 後から追加された行のボタンにも効くようにする)
    tbody.addEventListener('click', function (e) {
        if (e.target.classList.contains('btn-remove-row')) {
            const rows = tbody.querySelectorAll('tr');
            if (rows.length > 1) {
                e.target.closest('tr').remove();
            } else {
                alert('最低1行は必要です。');
            }
        }
    });

    // Enterキーによる誤送信を防ぐ(textarea内の改行は許可する)
    form.addEventListener('keydown', function (e) {
        if (e.key === 'Enter' && e.target.tagName !== 'TEXTAREA') {
            e.preventDefault();
        }
    });

    // 送信時の確認ダイアログ(呼び出し元でconfirmMessageOnSubmitが設定されている場合のみ)
    if (window.confirmMessageOnSubmit) {
        form.addEventListener('submit', function (e) {
            if (!confirm(window.confirmMessageOnSubmit)) {
                e.preventDefault();
            }
        });
    }
});
