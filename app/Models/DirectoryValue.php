<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\DirectoryValue
 *
 * @property int $id
 * @property int $directory_id
 * @property string $value
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|DirectoryValue newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|DirectoryValue newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|DirectoryValue query()
 * @method static \Illuminate\Database\Eloquent\Builder|DirectoryValue whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DirectoryValue whereDirectoryId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DirectoryValue whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DirectoryValue whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DirectoryValue whereValue($value)
 * @mixin \Eloquent
 */
class DirectoryValue extends Model
{
    use HasFactory;
}
