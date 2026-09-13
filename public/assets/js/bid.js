/**
 * Handles real-time-ish updates on the auction detail page:
 * - Polls /auctions/{id}/state every 4s for highest bid / countdown / history
 * - Submits new bids via fetch() without a full page reload
 * - Live client-side countdown ticking every second between polls
 */
(function () {
    const root = document.getElementById('bid-app');
    if (!root) return;

    const auctionId = root.dataset.auctionId;
    const stateUrl = root.dataset.stateUrl;
    const bidUrl = root.dataset.bidUrl;
    const csrfName = root.dataset.csrfName;

    let csrfHash = root.dataset.csrfHash;
    let secondsRemaining = parseInt(root.dataset.secondsRemaining, 10) || 0;

    const highestEl = document.getElementById('highest-bid-amount');
    const leaderEl = document.getElementById('leader-initials');
    const countdownEl = document.getElementById('countdown');
    const statusBadge = document.getElementById('status-badge');
    const historyEl = document.getElementById('bid-history');
    const form = document.getElementById('bid-form');
    const amountInput = document.getElementById('bid-amount');
    const feedback = document.getElementById('bid-feedback');
    const extensionNote = document.getElementById('extension-note');

    function formatMoney(n) {
        return '$' + Number(n).toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    }

    function formatCountdown(sec) {
        if (sec <= 0) return 'Ended';
        const h = Math.floor(sec / 3600);
        const m = Math.floor((sec % 3600) / 60);
        const s = sec % 60;
        return (h > 0 ? h + 'h ' : '') + String(m).padStart(2, '0') + 'm ' + String(s).padStart(2, '0') + 's';
    }

    function tickCountdown() {
        if (secondsRemaining > 0) secondsRemaining--;
        if (countdownEl) {
            countdownEl.textContent = formatCountdown(secondsRemaining);
            countdownEl.classList.toggle('urgent', secondsRemaining > 0 && secondsRemaining <= 300);
        }
    }

    function applyState(state) {
        if (highestEl && state.current_highest !== null) {
            highestEl.textContent = formatMoney(state.current_highest);
        }
        if (leaderEl) {
            leaderEl.textContent = state.leader_initials ? state.leader_initials : '—';
        }
        if (typeof state.seconds_remaining === 'number') {
            secondsRemaining = state.seconds_remaining;
        }
        if (statusBadge && state.status) {
            statusBadge.textContent = state.status.toUpperCase();
            statusBadge.className = 'status-badge status-' + state.status;
        }
        if (historyEl && Array.isArray(state.history)) {
            historyEl.innerHTML = state.history.map(function (b, i) {
                return '<div class="bid-history-item' + (i === 0 ? ' leading' : '') + '">' +
                    '<strong>' + escapeHtml(b.user) + '</strong> bid ' + formatMoney(b.amount) +
                    '<div class="text-muted small">' + new Date(b.created_at.replace(' ', 'T')).toLocaleTimeString() + '</div>' +
                    '</div>';
            }).join('') || '<p class="text-muted">No bids yet — be the first!</p>';
        }
    }

    function escapeHtml(str) {
        const div = document.createElement('div');
        div.textContent = str;
        return div.innerHTML;
    }

    function poll() {
        fetch(stateUrl, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
            .then(function (r) { return r.json(); })
            .then(applyState)
            .catch(function () { /* silent - will retry next interval */ });
    }

    if (form) {
        form.addEventListener('submit', function (e) {
            e.preventDefault();
            feedback.className = 'mt-2';
            feedback.textContent = '';

            const body = new FormData();
            body.append('amount', amountInput.value);
            body.append(csrfName, csrfHash);

            fetch(bidUrl, { method: 'POST', body: body, headers: { 'X-Requested-With': 'XMLHttpRequest' } })
                .then(function (r) { return r.json().then(function (data) { return { ok: r.ok, data: data }; }); })
                .then(function (res) {
                    feedback.textContent = res.data.message;
                    feedback.className = 'mt-2 fw-semibold ' + (res.ok ? 'text-success' : 'text-danger');

                    if (res.ok && res.data.state) {
                        applyState(res.data.state);
                        amountInput.value = '';
                        if (res.data.state.status === 'extended' && extensionNote) {
                            extensionNote.classList.remove('d-none');
                            setTimeout(function () { extensionNote.classList.add('d-none'); }, 6000);
                        }
                    }
                })
                .catch(function () {
                    feedback.textContent = 'Network error — please try again.';
                    feedback.className = 'mt-2 fw-semibold text-danger';
                });
        });
    }

    setInterval(tickCountdown, 1000);
    setInterval(poll, 4000);
    poll();
})();
