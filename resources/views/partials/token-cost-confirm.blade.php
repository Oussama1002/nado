{{--
    Pop-up de confirmation de coût en tokens AVANT soumission d'un formulaire.

    Usage :
        @include('partials.token-cost-confirm', [
            'formId'   => 'dmdForm',        // id du <form> à intercepter
            'costKey'  => 'demande',        // clé de coût (tokens.cost.<costKey>)
            'service'  => __('messages.tokens.reason.demande'), // libellé du service
        ])

    Ne s'active que si : tokens activés, coût > 0, client connecté et NON illimité.
    (Les invités / soldes insuffisants sont déjà redirigés en amont par le
    contrôleur ; ce pop-up est une confirmation, pas un contrôle de sécurité.)
--}}
@php
    $tkcEnabled = feature_on('tokens.enabled');
    $tkcCost    = (int) settings("tokens.cost.$costKey", 0);
    $tkcClient  = auth('client')->user();
    $tkcShow    = $tkcEnabled && $tkcCost > 0 && $tkcClient && ! $tkcClient->hasUnlimitedTokens();
    $tkcBalance = $tkcClient ? (int) $tkcClient->tokens_balance : 0;
    $tkcEnough  = $tkcBalance >= $tkcCost;
    $tkcId      = 'tkc_'.$formId;
@endphp

@if($tkcShow)
<div id="{{ $tkcId }}" aria-hidden="true"
     style="display:none;position:fixed;inset:0;z-index:100000;background:rgba(15,23,42,.6);backdrop-filter:blur(2px);align-items:center;justify-content:center;padding:18px;">
    <div role="dialog" aria-modal="true" aria-labelledby="{{ $tkcId }}_t"
         style="background:#fff;max-width:400px;width:100%;border-radius:18px;overflow:hidden;box-shadow:0 24px 60px rgba(0,0,0,.35);">
        <div style="background:linear-gradient(135deg,#1459DD,#0b3fa6);color:#fff;padding:22px 24px;text-align:center;">
            <div style="font-size:40px;line-height:1;margin-bottom:4px;">🪙</div>
            <h3 id="{{ $tkcId }}_t" style="margin:0;font-size:19px;font-weight:900;">{{ __('messages.tokens.confirm_title') }}</h3>
        </div>
        <div style="padding:22px 24px 24px;text-align:center;">
            <p style="margin:0 0 6px;color:#334155;font-size:15px;font-weight:600;line-height:1.6;">
                {!! __('messages.tokens.confirm_text', ['cost' => '<b>'.$tkcCost.'</b>', 'service' => '<b>'.e($service).'</b>']) !!}
            </p>
            <p style="margin:0 0 18px;color:#64748b;font-size:13.5px;font-weight:600;">
                {{ __('messages.tokens.confirm_balance', ['balance' => $tkcBalance]) }}
            </p>

            <button type="button" id="{{ $tkcId }}_yes"
                    style="display:block;width:100%;background:#1459DD;color:#fff;border:none;border-radius:12px;padding:13px 18px;font-weight:800;font-size:15px;cursor:pointer;margin-bottom:10px;">
                {{ __('messages.tokens.confirm_yes', ['cost' => $tkcCost]) }}
            </button>
            <button type="button" id="{{ $tkcId }}_no"
                    style="background:none;border:none;color:#94a3b8;font-weight:700;font-size:14px;cursor:pointer;">
                {{ __('messages.tokens.confirm_no') }}
            </button>
        </div>
    </div>
</div>
<script>
    (function () {
        var form    = document.getElementById(@json($formId));
        var modal   = document.getElementById(@json($tkcId));
        if (!form || !modal) return;
        var confirmed = false;

        form.addEventListener('submit', function (e) {
            if (confirmed) return;              // déjà confirmé → laisse passer
            if (!form.checkValidity()) return;  // laisse le navigateur signaler les champs requis
            e.preventDefault();
            modal.style.display = 'flex';
            modal.setAttribute('aria-hidden', 'false');
        });

        function close() { modal.style.display = 'none'; modal.setAttribute('aria-hidden', 'true'); }

        document.getElementById(@json($tkcId.'_yes')).addEventListener('click', function () {
            confirmed = true;
            close();
            if (typeof form.requestSubmit === 'function') form.requestSubmit(); else form.submit();
        });
        document.getElementById(@json($tkcId.'_no')).addEventListener('click', close);
        modal.addEventListener('click', function (e) { if (e.target === modal) close(); });
        document.addEventListener('keydown', function (e) { if (e.key === 'Escape') close(); });
    })();
</script>
@endif
