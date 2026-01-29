<?php

/**
 * Model Column Audit Script
 * 
 * This script analyzes all models to find columns being used in code
 * that may be missing from migrations.
 */

$modelsDir = __DIR__ . '/../../app/Models';
$migrationsDir = __DIR__ . '/../../database/migrations';
$controllersDir = __DIR__ . '/../../app/Http/Controllers';
$viewsDir = __DIR__ . '/../../resources/views';

$issues = [];

// Get all model files
$modelFiles = glob($modelsDir . '/*.php') + glob($modelsDir . '/**/*.php');

foreach ($modelFiles as $modelFile) {
    $modelName = basename($modelFile, '.php');

    // Skip non-model files
    if (in_array($modelName, ['User', 'Seller'])) {
        continue; // These use special auth tables
    }

    // Get table name (convert PascalCase to snake_case and pluralize)
    $tableName = strtolower(preg_replace('/(?<!^)[A-Z]/', '_$0', $modelName));
    $tableName = str_replace('_', '_', $tableName);

    // Find migration file for this table
    $migrationPattern = $migrationsDir . '/*_create_' . $tableName . 's_table.php';
    $migrationFiles = glob($migrationPattern);

    if (empty($migrationFiles)) {
        // Try singular
        $migrationPattern = $migrationsDir . '/*_create_' . $tableName . '_table.php';
        $migrationFiles = glob($migrationPattern);
    }

    if (empty($migrationFiles)) {
        continue; // No migration found
    }

    $migrationFile = $migrationFiles[0];
    $migrationContent = file_get_contents($migrationFile);

    // Extract columns from migration
    preg_match_all('/\$table->[\w]+\([\'"]([\w_]+)[\'"]/', $migrationContent, $matches);
    $migrationColumns = $matches[1] ?? [];

    // Find all references to this model in controllers
    $searchPattern = '/\$[\w]+->([a-z_]+)/';

    // Search in controllers
    $controllerFiles = glob($controllersDir . '/*.php') + glob($controllersDir . '/**/*.php');
    $referencedColumns = [];

    foreach ($controllerFiles as $controllerFile) {
        $content = file_get_contents($controllerFile);

        // Look for model usage
        if (strpos($content, $modelName . '::') !== false || strpos($content, 'use App\\Models\\' . $modelName) !== false) {
            preg_match_all($searchPattern, $content, $columnMatches);
            if (!empty($columnMatches[1])) {
                $referencedColumns = array_merge($referencedColumns, $columnMatches[1]);
            }
        }
    }

    $referencedColumns = array_unique($referencedColumns);

    // Find missing columns
    $missingColumns = array_diff($referencedColumns, $migrationColumns);

    // Filter out common methods and relations
    $commonMethods = ['save', 'delete', 'update', 'create', 'first', 'all', 'find', 'where', 'with', 'belongsTo', 'hasMany', 'hasOne'];
    $missingColumns = array_filter($missingColumns, function ($col) use ($commonMethods) {
        return !in_array($col, $commonMethods) && strlen($col) > 2;
    });

    if (!empty($missingColumns)) {
        $issues[$modelName] = [
            'table' => $tableName,
            'migration' => basename($migrationFile),
            'missing_columns' => array_values($missingColumns),
            'existing_columns' => $migrationColumns
        ];
    }
}

// Output results
echo "# Model Column Audit Report\n\n";
echo "Total models analyzed: " . count($modelFiles) . "\n";
echo "Models with potential issues: " . count($issues) . "\n\n";

foreach ($issues as $model => $data) {
    echo "## $model\n";
    echo "Table: {$data['table']}\n";
    echo "Migration: {$data['migration']}\n";
    echo "Missing columns: " . implode(', ', $data['missing_columns']) . "\n\n";
}

// Save to file
file_put_contents(__DIR__ . '/column_audit_report.txt', ob_get_clean());
echo "Report saved to database/seeders/column_audit_report.txt\n";
