import Alpine from 'alpinejs'

const K = new Uint32Array([
    0x428a2f98, 0x71374491, 0xb5c0fbcf, 0xe9b5dba5, 0x3956c25b, 0x59f111f1, 0x923f82a4, 0xab1c5ed5, 0xd807aa98, 0x12835b01,
    0x243185be, 0x550c7dc3, 0x72be5d74, 0x80deb1fe, 0x9bdc06a7, 0xc19bf174, 0xe49b69c1, 0xefbe4786, 0x0fc19dc6, 0x240ca1cc,
    0x2de92c6f, 0x4a7484aa, 0x5cb0a9dc, 0x76f988da, 0x983e5152, 0xa831c66d, 0xb00327c8, 0xbf597fc7, 0xc6e00bf3, 0xd5a79147,
    0x06ca6351, 0x14292967, 0x27b70a85, 0x2e1b2138, 0x4d2c6dfc, 0x53380d13, 0x650a7354, 0x766a0abb, 0x81c2c92e, 0x92722c85,
    0xa2bfe8a1, 0xa81a664b, 0xc24b8b70, 0xc76c51a3, 0xd192e819, 0xd6990624, 0xf40e3585, 0x106aa070, 0x19a4c116, 0x1e376c08,
    0x2748774c, 0x34b0bcb5, 0x391c0cb3, 0x4ed8aa4a, 0x5b9cca4f, 0x682e6ff3, 0x748f82ee, 0x78a5636f, 0x84c87814, 0x8cc70208,
    0x90befffa, 0xa4506ceb, 0xbef9a3f7, 0xc67178f2,
])

/**
 * Plain JavaScript SHA-256, used when Web Crypto is unavailable (pages served over plain HTTP).
 */
export function sha256Fallback(text) {
    const bytes = new TextEncoder().encode(text)
    const length = Math.ceil((bytes.length + 9) / 64) * 64
    const data = new Uint8Array(length)
    data.set(bytes)
    data[bytes.length] = 0x80

    const view = new DataView(data.buffer)
    view.setUint32(length - 8, Math.floor((bytes.length * 8) / 0x100000000))
    view.setUint32(length - 4, (bytes.length * 8) >>> 0)

    const hash = new Uint32Array([
        0x6a09e667, 0xbb67ae85, 0x3c6ef372, 0xa54ff53a, 0x510e527f, 0x9b05688c, 0x1f83d9ab, 0x5be0cd19,
    ])
    const w = new Uint32Array(64)
    const rotr = (x, n) => (x >>> n) | (x << (32 - n))

    for (let offset = 0; offset < length; offset += 64) {
        for (let i = 0; i < 16; i++) {
            w[i] = view.getUint32(offset + i * 4)
        }

        for (let i = 16; i < 64; i++) {
            const s0 = rotr(w[i - 15], 7) ^ rotr(w[i - 15], 18) ^ (w[i - 15] >>> 3)
            const s1 = rotr(w[i - 2], 17) ^ rotr(w[i - 2], 19) ^ (w[i - 2] >>> 10)
            w[i] = w[i - 16] + s0 + w[i - 7] + s1
        }

        let [a, b, c, d, e, f, g, h] = hash

        for (let i = 0; i < 64; i++) {
            const t1 = h + (rotr(e, 6) ^ rotr(e, 11) ^ rotr(e, 25)) + ((e & f) ^ (~e & g)) + K[i] + w[i]
            const t2 = (rotr(a, 2) ^ rotr(a, 13) ^ rotr(a, 22)) + ((a & b) ^ (a & c) ^ (b & c))
            h = g
            g = f
            f = e
            e = (d + t1) >>> 0
            d = c
            c = b
            b = a
            a = (t1 + t2) >>> 0
        }

        hash[0] += a
        hash[1] += b
        hash[2] += c
        hash[3] += d
        hash[4] += e
        hash[5] += f
        hash[6] += g
        hash[7] += h
    }

    return Array.from(hash, (word) => word.toString(16).padStart(8, '0')).join('')
}

async function sha256(text) {
    if (!globalThis.crypto?.subtle) {
        return sha256Fallback(text)
    }

    const digest = await crypto.subtle.digest('SHA-256', new TextEncoder().encode(text))

    return Array.from(new Uint8Array(digest), (byte) => byte.toString(16).padStart(2, '0')).join('')
}

/**
 * Solves an ALTCHA proof-of-work challenge and keeps the form's submit buttons
 * disabled until a solution is ready.
 */
Alpine.data('cachetAltcha', (challengeUrl) => ({
    state: 'verifying',
    payload: '',

    init() {
        const form = this.$el.closest('form')

        form?.addEventListener('submit', (event) => {
            if (this.state !== 'verified') {
                event.preventDefault()
            }
        })

        this.$watch('state', () => this.toggleButtons(form))
        this.toggleButtons(form)
        this.run()
    },

    toggleButtons(form) {
        form?.querySelectorAll('[type="submit"]').forEach((button) => {
            button.disabled = this.state !== 'verified'
        })
    },

    async run() {
        this.state = 'verifying'

        try {
            const expires = await this.solve()

            this.state = 'verified'

            // Fetch a fresh challenge shortly before this one expires.
            setTimeout(() => this.run(), Math.max(expires * 1000 - Date.now() - 15000, 15000))
        } catch {
            this.state = 'error'
        }
    },

    async solve() {
        const response = await fetch(challengeUrl, {
            headers: { Accept: 'application/json' },
            credentials: 'same-origin',
        })

        if (!response.ok) {
            throw new Error('Unable to load the challenge.')
        }

        const challenge = await response.json()

        for (let number = 0; number <= challenge.maxnumber; number++) {
            if ((await sha256(challenge.salt + number)) === challenge.challenge) {
                this.payload = btoa(
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
    },
}))
