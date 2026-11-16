# End-to-end tests

The result-form end-to-end tests use a separate Docker Compose project. It has its own MySQL database, Redis instance, Laravel app port, Reverb port, dependency volumes and deterministic test data. It does not use the normal local database or the live site.

From the project root:

```sh
npm run e2e:run
```

The stack is deliberately left running after the tests so the result form can be inspected at [http://127.0.0.1:18000](http://127.0.0.1:18000). Realtime events use port `18081`.

To start the site without running tests:

```sh
npm run e2e:up
```

To stop it:

```sh
npm run e2e:down
```

`npm run e2e:up` recreates the bootstrap container and runs `migrate:fresh`, so the two fixtures and the two Team Admin accounts are reset on every start. The test password is `password`; the accounts are `e2e-home@example.test` and `e2e-away@example.test`.

If Playwright does not already have a browser available, install one once with:

```sh
npm run e2e:install
```

The browser tests cover the empty score state, selecting and resetting a frame winner, presence over Reverb, and locking/releasing a player field across two independent sessions.
