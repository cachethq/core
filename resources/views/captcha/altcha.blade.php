<div
    data-captcha="altcha"
    data-challenge-url="{{ $challengeUrl }}"
    data-state="verifying"
    class="flex items-center gap-2 rounded-md px-3 py-2 text-sm text-zinc-500 ring-1 ring-inset ring-zinc-300 dark:text-zinc-400 dark:ring-zinc-700"
>
    <input type="hidden" name="altcha" value="" />
    <x-heroicon-o-shield-check class="size-5 shrink-0" aria-hidden="true" />
    <span data-captcha-message="verifying" role="status">{{ __('cachet::subscriber.status_page.captcha.verifying') }}</span>
    <span data-captcha-message="verified" role="status" hidden>{{ __('cachet::subscriber.status_page.captcha.verified') }}</span>
    <span data-captcha-message="error" role="alert" hidden>{{ __('cachet::subscriber.status_page.captcha.error') }}</span>
    <noscript>{{ __('cachet::subscriber.status_page.captcha.noscript') }}</noscript>
</div>

<script>
    (() => {
        const widget = document.currentScript.previousElementSibling
        const form = widget.closest('form')
        const input = widget.querySelector('input[name="altcha"]')
        const buttons = form ? form.querySelectorAll('[type="submit"]') : []

        const setState = (state) => {
            widget.dataset.state = state
            widget.querySelectorAll('[data-captcha-message]').forEach((message) => {
                message.hidden = message.dataset.captchaMessage !== state
            })
            buttons.forEach((button) => {
                button.disabled = state !== 'verified'
                button.style.opacity = button.disabled ? '0.5' : ''
                button.style.cursor = button.disabled ? 'not-allowed' : ''
            })
        }

        const sha256 = async (text) => {
            const digest = await crypto.subtle.digest('SHA-256', new TextEncoder().encode(text))

            return Array.from(new Uint8Array(digest), (byte) => byte.toString(16).padStart(2, '0')).join('')
        }

        const solve = async () => {
            setState('verifying')

            const response = await fetch(widget.dataset.challengeUrl, {
                headers: { Accept: 'application/json' },
                credentials: 'same-origin',
            })

            if (!response.ok) {
                throw new Error('Unable to load the challenge.')
            }

            const challenge = await response.json()

            for (let number = 0; number <= challenge.maxnumber; number++) {
                if ((await sha256(challenge.salt + number)) === challenge.challenge) {
                    input.value = btoa(
                        JSON.stringify({
                            algorithm: challenge.algorithm,
                            challenge: challenge.challenge,
                            number,
                            salt: challenge.salt,
                            signature: challenge.signature,
                        }),
                    )

                    return Number(new URLSearchParams(challenge.salt.split('?')[1]).get('expires'))
                }
            }

            throw new Error('Unable to solve the challenge.')
        }

        const run = () =>
            solve()
                .then((expires) => {
                    setState('verified')

                    // Fetch a fresh challenge shortly before this one expires.
                    setTimeout(run, Math.max(expires * 1000 - Date.now() - 15000, 15000))
                })
                .catch(() => setState('error'))

        run()
    })()
</script>
