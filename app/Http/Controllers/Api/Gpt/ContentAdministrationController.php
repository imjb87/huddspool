<?php

namespace App\Http\Controllers\Api\Gpt;

use App\Http\Controllers\Controller;
use App\Models\GptActionAudit;
use App\Models\News;
use App\Models\Page;
use App\Models\Ruleset;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class ContentAdministrationController extends Controller
{
    public function store(Request $request, string $resource): JsonResponse
    {
        [$model, $data] = $this->validated($request, $resource);
        if ($model === Ruleset::class) {
            $data['slug'] = $this->uniqueSlug(Ruleset::class, $data['name']);
            $record = new Ruleset;
            $record->forceFill($data)->save();
        } else {
            $record = $model::query()->create($data);
        }
        $audit = $this->audit($request, 'create_'.$resource, $record, null, $this->summary($record));

        return response()->json(['message' => 'The content record was created.', 'resource' => $resource, 'record_id' => $record->id, 'audit_id' => $audit->id], 201);
    }

    public function update(Request $request, string $resource, int $record): JsonResponse
    {
        [$model, $data] = $this->validated($request, $resource, $record);
        $expected = Carbon::parse($data['expected_updated_at']);
        unset($data['expected_updated_at']);
        $record = $model::query()->findOrFail($record);
        if (! $record->updated_at?->equalTo($expected)) {
            throw ValidationException::withMessages(['expected_updated_at' => 'The content changed after it was inspected. Inspect it again before retrying.']);
        }
        $before = $this->summary($record);
        if ($record instanceof Ruleset && isset($data['name']) && $data['name'] !== $record->name) {
            $data['slug'] = $this->uniqueSlug(Ruleset::class, $data['name'], $record->id);
            $record->forceFill($data)->save();
        } else {
            $record->update($data);
        }
        $audit = $this->audit($request, 'update_'.$resource, $record, $before, $this->summary($record->refresh()));

        return response()->json(['message' => 'The content record was updated.', 'resource' => $resource, 'record_id' => $record->id, 'audit_id' => $audit->id]);
    }

    private function validated(Request $request, string $resource, ?int $record = null): array
    {
        $sometimes = $record ? 'sometimes' : 'required';

        return match ($resource) {
            'news' => [News::class, $request->validate(array_filter(['expected_updated_at' => $record ? ['required', 'date'] : null, 'title' => [$sometimes, 'string', 'max:255'], 'content' => [$sometimes, 'string'], 'published_at' => ['nullable', 'date']]))],
            'pages' => [Page::class, $request->validate(array_filter(['expected_updated_at' => $record ? ['required', 'date'] : null, 'title' => [$sometimes, 'string', 'max:255'], 'slug' => [$sometimes, 'string', 'max:255', Rule::unique('pages', 'slug')->ignore($record)], 'content' => [$sometimes, 'string']]))],
            'rulesets' => [Ruleset::class, $request->validate(array_filter(['expected_updated_at' => $record ? ['required', 'date'] : null, 'name' => [$sometimes, 'string', 'max:255', Rule::unique('rulesets', 'name')->ignore($record)], 'content' => [$sometimes, 'string']]))],
            default => throw ValidationException::withMessages(['resource' => 'Supported content resources are news, pages and rulesets.']),
        };
    }

    private function summary(Model $record): array
    {
        $body = (string) ($record->content ?? '');

        return ['title' => $record->title ?? $record->name, 'slug' => $record->slug ?? null, 'published_at' => $record->published_at?->toAtomString(), 'content_length' => strlen($body), 'content_sha256' => hash('sha256', $body), 'updated_at' => $record->updated_at?->toAtomString()];
    }

    private function uniqueSlug(string $model, string $name, ?int $ignore = null): string
    {
        $base = Str::slug($name) ?: 'ruleset';
        $slug = $base;
        $index = 1;
        while ($model::query()->when($ignore, fn ($query) => $query->whereKeyNot($ignore))->where('slug', $slug)->exists()) {
            $slug = $base.'-'.$index++;
        }

        return $slug;
    }

    private function audit(Request $request, string $action, Model $record, ?array $before, array $after): GptActionAudit
    {
        return GptActionAudit::query()->create(['administrator_id' => $request->user()->id, 'action' => $action, 'subject_type' => $record::class, 'subject_id' => $record->id, 'before' => $before, 'after' => $after, 'ip_address' => $request->ip(), 'user_agent' => $request->userAgent()]);
    }
}
