<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class MakeModuleCommand extends Command
{
    protected $signature   = 'make:module {module : The module name (e.g. Tenant)}';
    protected $description = 'Scaffold a new module with Controller, Model, Service, DTO, and Request classes';

    // Prefixed with "mod" to avoid collision with Command::$name
    private string $modStudly;
    private string $modCamel;
    private string $modSnake;
    private string $modPlural;
    private string $modPluralSnake;
    private string $modNamespace;
    private string $modPath;

    public function handle(): int
    {
        $raw = trim($this->argument('module'));

        if (empty($raw)) {
            $this->error('Module name cannot be empty.');
            return self::FAILURE;
        }

        if (! preg_match('/^[A-Za-z][A-Za-z0-9]*$/', $raw)) {
            $this->error('Module name must be alphanumeric and start with a letter (e.g. Tenant, PayerAccount).');
            return self::FAILURE;
        }

        $this->modStudly      = Str::studly($raw);
        $this->modCamel       = Str::camel($raw);
        $this->modSnake       = Str::snake($raw);
        $this->modPlural      = Str::plural($this->modStudly);
        $this->modPluralSnake = Str::snake($this->modPlural);
        $this->modNamespace   = "App\\Modules\\{$this->modStudly}";
        $this->modPath        = app_path("Modules/{$this->modStudly}");

        if (File::isDirectory($this->modPath)) {
            $this->error("Module [{$this->modStudly}] already exists at app/Modules/{$this->modStudly}.");
            return self::FAILURE;
        }

        $this->info("Creating module <comment>{$this->modStudly}</comment>...");
        $this->newLine();

        try {
            $this->makeDirectories();
            $this->makeModel();
            $this->makeDTO();
            $this->makeCreateRequest();
            $this->makeUpdateRequest();
            $this->makeService();
            $this->makeController();
        } catch (\Throwable $e) {
            $this->newLine();
            $this->error("Failed: {$e->getMessage()}");
            $this->line("  <fg=yellow>Rolling back — removing app/Modules/{$this->modStudly}</>");
            File::deleteDirectory($this->modPath);
            return self::FAILURE;
        }

        $this->newLine();
        $this->info("✓ Module <comment>{$this->modStudly}</comment> scaffolded successfully.");
        $this->newLine();
        $this->line("  <fg=gray>app/Modules/{$this->modStudly}/</>");
        $this->line("  ├── Models/{$this->modStudly}.php");
        $this->line("  ├── DTOs/{$this->modStudly}DTO.php");
        $this->line("  ├── Services/{$this->modStudly}Service.php");
        $this->line("  ├── Http/Controllers/{$this->modStudly}Controller.php");
        $this->line("  ├── Http/Requests/Create{$this->modStudly}Request.php");
        $this->line("  └── Http/Requests/Update{$this->modStudly}Request.php");
        $this->newLine();
        $this->line("  <fg=yellow>Register the route in routes/api.php:</>");
        $this->line("  Route::apiResource('{$this->modPluralSnake}', \\{$this->modNamespace}\\Http\\Controllers\\{$this->modStudly}Controller::class);");
        $this->newLine();

        return self::SUCCESS;
    }

    // -----------------------------------------------------------------------
    //  Directories
    // -----------------------------------------------------------------------

    private function makeDirectories(): void
    {
        $dirs = [
            'Models',
            'DTOs',
            'Services',
            'Http/Controllers',
            'Http/Requests',
        ];

        foreach ($dirs as $dir) {
            File::makeDirectory("{$this->modPath}/{$dir}", 0755, true, true);
            $this->line("  <fg=blue>+</> <fg=gray>app/Modules/{$this->modStudly}/{$dir}/</>");
        }
    }

    // -----------------------------------------------------------------------
    //  Stubs
    // -----------------------------------------------------------------------

    private function makeModel(): void
    {
        $stub = <<<PHP
        <?php

        namespace {ns}\Models;

        use Illuminate\Database\Eloquent\Factories\HasFactory;
        use Illuminate\Database\Eloquent\Model;
        use Illuminate\Database\Eloquent\SoftDeletes;

        class {class} extends Model
        {
            use HasFactory, SoftDeletes;

            protected \$table = '{table}';

            protected \$fillable = [
                // TODO: add fillable columns
            ];

            protected \$casts = [
                // TODO: add casts
            ];
        }
        PHP;

        $this->write("Models/{$this->modStudly}.php", $this->interpolate($stub, [
            '{ns}'    => $this->modNamespace,
            '{class}' => $this->modStudly,
            '{table}' => $this->modPluralSnake,
        ]));
    }

    private function makeDTO(): void
    {
        $stub = <<<PHP
        <?php

        namespace {ns}\DTOs;

        class {class}DTO
        {
            public function __construct(
                // TODO: add typed properties
                // public readonly string \$name,
            ) {}

            public static function fromArray(array \$data): self
            {
                return new self(
                    // \$data['name'],
                );
            }

            public function toArray(): array
            {
                return [
                    // 'name' => \$this->name,
                ];
            }
        }
        PHP;

        $this->write("DTOs/{$this->modStudly}DTO.php", $this->interpolate($stub, [
            '{ns}'    => $this->modNamespace,
            '{class}' => $this->modStudly,
        ]));
    }

    private function makeCreateRequest(): void
    {
        $stub = <<<PHP
        <?php

        namespace {ns}\Http\Requests;

        use Illuminate\Foundation\Http\FormRequest;

        class Create{class}Request extends FormRequest
        {
            public function authorize(): bool
            {
                return true;
            }

            public function rules(): array
            {
                return [
                    // TODO: define validation rules
                    // 'name' => ['required', 'string', 'max:255'],
                ];
            }
        }
        PHP;

        $this->write("Http/Requests/Create{$this->modStudly}Request.php", $this->interpolate($stub, [
            '{ns}'    => $this->modNamespace,
            '{class}' => $this->modStudly,
        ]));
    }

    private function makeUpdateRequest(): void
    {
        $stub = <<<PHP
        <?php

        namespace {ns}\Http\Requests;

        use Illuminate\Foundation\Http\FormRequest;

        class Update{class}Request extends FormRequest
        {
            public function authorize(): bool
            {
                return true;
            }

            public function rules(): array
            {
                return [
                    // TODO: define validation rules
                    // 'name' => ['sometimes', 'string', 'max:255'],
                ];
            }
        }
        PHP;

        $this->write("Http/Requests/Update{$this->modStudly}Request.php", $this->interpolate($stub, [
            '{ns}'    => $this->modNamespace,
            '{class}' => $this->modStudly,
        ]));
    }

    private function makeService(): void
    {
        $stub = <<<PHP
        <?php

        namespace {ns}\Services;

        use {ns}\DTOs\{class}DTO;
        use {ns}\Models\{class};
        use Illuminate\Pagination\LengthAwarePaginator;

        class {class}Service
        {
            public function paginate(int \$perPage = 15): LengthAwarePaginator
            {
                return {class}::latest()->paginate(\$perPage);
            }

            public function findOrFail(int|string \$id): {class}
            {
                return {class}::findOrFail(\$id);
            }

            public function create({class}DTO \$dto): {class}
            {
                return {class}::create(\$dto->toArray());
            }

            public function update({class} \$model, {class}DTO \$dto): {class}
            {
                \$model->update(\$dto->toArray());

                return \$model->fresh();
            }

            public function delete({class} \$model): void
            {
                \$model->delete();
            }
        }
        PHP;

        $this->write("Services/{$this->modStudly}Service.php", $this->interpolate($stub, [
            '{ns}'    => $this->modNamespace,
            '{class}' => $this->modStudly,
        ]));
    }

    private function makeController(): void
    {
        $stub = <<<PHP
        <?php

        namespace {ns}\Http\Controllers;

        use App\Http\Controllers\Controller;
        use {ns}\DTOs\{class}DTO;
        use {ns}\Http\Requests\Create{class}Request;
        use {ns}\Http\Requests\Update{class}Request;
        use {ns}\Models\{class};
        use {ns}\Services\{class}Service;
        use Illuminate\Http\JsonResponse;
        use Illuminate\Http\Request;

        class {class}Controller extends Controller
        {
            public function __construct(
                private readonly {class}Service \$service,
            ) {}

            /** GET /api/{route} */
            public function index(Request \$request): JsonResponse
            {
                \$data = \$this->service->paginate(
                    (int) \$request->query('per_page', 15)
                );

                return response()->json(\$data);
            }

            /** POST /api/{route} */
            public function store(Create{class}Request \$request): JsonResponse
            {
                \$dto    = {class}DTO::fromArray(\$request->validated());
                \$result = \$this->service->create(\$dto);

                return response()->json(\$result, 201);
            }

            /** GET /api/{route}/{id} */
            public function show(int|string \$id): JsonResponse
            {
                \$model = \$this->service->findOrFail(\$id);

                return response()->json(\$model);
            }

            /** PUT|PATCH /api/{route}/{id} */
            public function update(Update{class}Request \$request, int|string \$id): JsonResponse
            {
                \$model  = \$this->service->findOrFail(\$id);
                \$dto    = {class}DTO::fromArray(\$request->validated());
                \$result = \$this->service->update(\$model, \$dto);

                return response()->json(\$result);
            }

            /** DELETE /api/{route}/{id} */
            public function destroy(int|string \$id): JsonResponse
            {
                \$model = \$this->service->findOrFail(\$id);
                \$this->service->delete(\$model);

                return response()->json(null, 204);
            }
        }
        PHP;

        $this->write("Http/Controllers/{$this->modStudly}Controller.php", $this->interpolate($stub, [
            '{ns}'    => $this->modNamespace,
            '{class}' => $this->modStudly,
            '{route}' => $this->modPluralSnake,
        ]));
    }

    // -----------------------------------------------------------------------
    //  Helpers
    // -----------------------------------------------------------------------

    /**
     * Replace {tokens} in a heredoc stub and strip leading indentation.
     */
    private function interpolate(string $stub, array $replacements): string
    {
        // Strip the 8-space heredoc indent added by the method body
        $stub = preg_replace('/^        /m', '', $stub);

        return str_replace(
            array_keys($replacements),
            array_values($replacements),
            $stub
        );
    }

    private function write(string $relativePath, string $content): void
    {
        $fullPath = "{$this->modPath}/{$relativePath}";

        File::put($fullPath, $content);
        $this->line("  <fg=green>✓</> Created <fg=gray>app/Modules/{$this->modStudly}/{$relativePath}</>");
    }
}