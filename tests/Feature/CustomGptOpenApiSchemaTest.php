<?php

namespace Tests\Feature;

use App\Http\Controllers\Api\Gpt\AdministrationCommandController;
use ReflectionClass;
use Symfony\Component\Yaml\Yaml;
use Tests\TestCase;

class CustomGptOpenApiSchemaTest extends TestCase
{
    public function test_schema_defines_the_oauth_contract(): void
    {
        $schema = $this->schema();

        $this->assertSame(
            'https://www.huddspool.co.uk/oauth/authorize',
            data_get($schema, 'components.securitySchemes.oauth.flows.authorizationCode.authorizationUrl'),
        );
        $this->assertSame(
            'https://www.huddspool.co.uk/oauth/token',
            data_get($schema, 'components.securitySchemes.oauth.flows.authorizationCode.tokenUrl'),
        );
        $this->assertSame(
            ['gpt:read', 'gpt:write'],
            data_get($schema, 'security.0.oauth'),
        );
    }

    public function test_every_administration_command_has_a_specific_argument_schema(): void
    {
        $schema = $this->schema();
        $commandSchemas = data_get($schema, 'paths./command.post.requestBody.content.application/json.schema.oneOf');
        $documentedCommands = collect($commandSchemas)
            ->pluck('properties.command.const')
            ->sort()
            ->values()
            ->all();
        $controllerCommands = array_keys(
            (new ReflectionClass(AdministrationCommandController::class))
                ->getReflectionConstant('COMMANDS')
                ->getValue(),
        );
        sort($controllerCommands);

        $this->assertSame($controllerCommands, $documentedCommands);

        foreach ($commandSchemas as $commandSchema) {
            $reference = data_get($commandSchema, 'properties.arguments.$ref');

            $this->assertIsString($reference);
            $this->assertArrayHasKey(
                basename(str_replace('#/components/schemas/', '', $reference)),
                data_get($schema, 'components.schemas'),
            );
        }
    }

    public function test_move_player_command_documents_its_state_guard_and_identifiers(): void
    {
        $schema = $this->schema();
        $arguments = data_get($schema, 'components.schemas.MovePlayerArguments');

        $this->assertSame(
            ['player', 'destination_team_id', 'expected_current_team_id'],
            data_get($arguments, 'required'),
        );
        $this->assertSame('integer', data_get($arguments, 'properties.player.type'));
        $this->assertSame('integer', data_get($arguments, 'properties.destination_team_id.type'));
        $this->assertSame('boolean', data_get($arguments, 'properties.make_destination_captain.type'));
    }

    /**
     * @return array<string, mixed>
     */
    private function schema(): array
    {
        return Yaml::parseFile(base_path('docs/custom-gpt/openapi.yaml'));
    }
}
