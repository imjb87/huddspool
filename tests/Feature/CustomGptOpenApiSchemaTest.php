<?php

namespace Tests\Feature;

use App\Http\Controllers\Api\Gpt\AdministrationCommandController;
use Illuminate\Support\Str;
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

    public function test_every_administration_command_exposes_its_fields_in_one_flat_argument_object(): void
    {
        $schema = $this->schema();
        $requestSchema = data_get($schema, 'paths./command.post.requestBody.content.application/json.schema');
        $argumentsSchema = data_get($requestSchema, 'properties.arguments');
        $argumentProperties = data_get($argumentsSchema, 'properties');
        $documentedCommands = data_get($requestSchema, 'properties.command.enum');
        sort($documentedCommands);
        $controllerCommands = array_keys(
            (new ReflectionClass(AdministrationCommandController::class))
                ->getReflectionConstant('COMMANDS')
                ->getValue(),
        );
        sort($controllerCommands);

        $this->assertSame('object', data_get($requestSchema, 'type'));
        $this->assertSame(['command', 'arguments'], data_get($requestSchema, 'required'));
        $this->assertSame('object', data_get($argumentsSchema, 'type'));
        $this->assertArrayNotHasKey('oneOf', $argumentsSchema);
        $this->assertFalse(data_get($argumentsSchema, 'additionalProperties'));
        $this->assertSame($controllerCommands, $documentedCommands);

        foreach ($controllerCommands as $command) {
            $componentName = Str::studly($command).'Arguments';
            $commandProperties = data_get($schema, "components.schemas.{$componentName}.properties");

            $this->assertIsArray($commandProperties, "{$componentName} must define properties.");
            $this->assertEmpty(
                array_diff(array_keys($commandProperties), array_keys($argumentProperties)),
                "{$componentName} contains fields hidden from the flattened Action schema.",
            );
        }
    }

    public function test_move_player_command_documents_its_state_guard_and_identifiers(): void
    {
        $schema = $this->schema();
        $arguments = data_get($schema, 'paths./command.post.requestBody.content.application/json.schema.properties.arguments');

        $this->assertSame('integer', data_get($arguments, 'properties.player.type'));
        $this->assertSame(
            ['integer', 'null'],
            array_column(data_get($arguments, 'properties.destination_team_id.oneOf'), 'type'),
        );
        $this->assertStringContainsString('leave them unassigned', data_get($arguments, 'properties.destination_team_id.description'));
        $this->assertSame('boolean', data_get($arguments, 'properties.make_destination_captain.type'));
        $this->assertStringContainsString('move_player', data_get($arguments, 'properties.player.description'));
        $this->assertStringContainsString('Required for: move_player', data_get($arguments, 'properties.expected_current_team_id.description'));
    }

    public function test_fixture_venue_commands_document_their_state_guards_and_opt_in_propagation_flag(): void
    {
        $schema = $this->schema();
        $arguments = data_get($schema, 'paths./command.post.requestBody.content.application/json.schema.properties.arguments');

        $this->assertSame('boolean', data_get($arguments, 'properties.update_future_home_fixtures.type'));
        $this->assertStringContainsString('set_team_venue', data_get($arguments, 'properties.update_future_home_fixtures.description'));
        $this->assertStringContainsString('set_fixture_venue', data_get($arguments, 'properties.fixture.description'));
        $this->assertStringContainsString('set_fixture_venue', data_get($arguments, 'properties.expected_current_venue_id.description'));
        $this->assertStringContainsString('set_fixture_venue', data_get($arguments, 'properties.expected_updated_at.description'));
        $this->assertStringContainsString('set_fixture_venue', data_get($arguments, 'properties.reason.description'));
    }

    /**
     * @return array<string, mixed>
     */
    private function schema(): array
    {
        return Yaml::parseFile(base_path('docs/custom-gpt/openapi.yaml'));
    }
}
