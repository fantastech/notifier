/* eslint-env node */
module.exports = {
  root: true,
  env: {
    amd: true,
    browser: true,
    es6: true,
    node: true,
    jquery: true,
  },
  extends: 'eslint:recommended',
  parser: 'babel-eslint',
  parserOptions: {
    ecmaFeatures: {
      generators: false,
      globalReturn: true,
      objectLiteralDuplicateProperties: false,
    },
    ecmaVersion: 2017,
    sourceType: 'module',
  },
  globals: {
    wp: true,
  },
  plugins: ['import'],
  settings: {
    'import/core-modules': [],
    'import/ignore': ['node_modules', '\\.(coffee|scss|css|less|hbs|svg|json)$'],
  },
  rules: {
    semi: [
      'error',
      'always',
    ],
    quotes: [
      'error',
      'single',
    ],
    'no-console': 0,
    'comma-dangle': [
      'error',
      {
        arrays: 'always-multiline',
        objects: 'always-multiline',
        imports: 'always-multiline',
        exports: 'always-multiline',
        functions: 'ignore',
      },
    ],
  },
};
