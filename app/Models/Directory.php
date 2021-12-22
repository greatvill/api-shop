<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Directory
 *
 * @property int $id
 * @property string $code
 * @property string $desc
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|Directory newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Directory newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Directory query()
 * @method static \Illuminate\Database\Eloquent\Builder|Directory whereCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Directory whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Directory whereDesc($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Directory whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Directory whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Directory extends Model
{
    use HasFactory;
}
