module.exports = {
    extends: [
        'eslint:recommended',
        'plugin:vue/vue3-recommended',
    ],
    ignorePatterns: ['ziggy.js'],
    parserOptions: {
        ecmaVersion: 2020,
        sourceType: 'module',
    },
    env: {
        amd: true,
        browser: true,
        es6: true,
    },
    rules: {
        indent: ['error', 2],
        quotes: ['warn', 'single'],
        semi: ['warn', 'never'],
        'comma-dangle': ['warn', 'always-multiline'],
        'no-unused-vars': ['error', { vars: 'all', args: 'after-used', ignoreRestSiblings: true }],
        'vue/component-name-in-template-casing': 'warn',
        'vue/html-self-closing': ['warn', { html: { void: 'always', normal: 'always', component: 'always' } }],
        'vue/max-attributes-per-line': 'off',
        'vue/multi-word-component-names': 'off',
        'vue/no-reserved-component-names': 'off',
        'vue/no-v-html': 'off',
        'vue/require-default-prop': 'off',
        'vue/singleline-html-element-content-newline': 'off',
    },
}
