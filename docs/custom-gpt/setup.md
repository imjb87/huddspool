# Custom GPT setup

This integration uses a confidential OAuth 2.0 authorization-code client. Each administrator signs in with their existing Huddspool account, and the API rejects any account that is not currently an administrator.

1. Deploy the feature and run the database migrations.
2. Generate Passport encryption keys in the live environment if they do not already exist.
3. In the GPT editor, create an Action and import `openapi.yaml`.
4. Choose OAuth authentication. Use the authorization and token URLs from the schema, request `gpt:read gpt:write`, and save once to obtain ChatGPT's callback URL.
5. Create the OAuth client on the live server:

   `php artisan passport:client --name="Huddspool administrator GPT" --redirect_uri="CHATGPT_CALLBACK_URL"`

6. Copy the one-time client ID and client secret into the GPT Action authentication settings.
7. Paste `instructions.md` into the GPT instructions and test with a non-destructive dashboard request before testing a confirmed player move.

Do not commit Passport private keys, the OAuth client secret, or live access tokens.
