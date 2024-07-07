# Development

## Local Development

### Setup
1. `npm run test:start` - Start the local development server (requires docker)
2. `npm run test:plugin:composer-install` - Composer install in the e2e test plugin. This symlinks the wp-enforce-semver dep in the e2e test plugin
3. `npm run test:plugin:activate` - This activates the e2e test plugin

### Interacting with the local environment

This will set up a local WordPress instance with a plugin `my-test-plugin` installed and activated that mocks an update response. You can change the version of the mocked update response with the following command:

```
npm run test:plugin:set-version 2.0.0
npm run test:plugin:set-version 1.1.0
npm run test:plugin:set-version 5.1.2
```

This allows you to determine all of the different states from patch/minor/major changes and what those different states look like.

## Running E2E tests

Running E2E tests will happen when you open a PR to the repo, but if you'd like to run them locally, you can use the following command:

```
npm run test
```

Or, if you'd like a UI view for the tests cases running in Playwright, you can run:

```
npm run test:ui
```

In order to run the tests, you must make sure that you have followed the setup steps above.
