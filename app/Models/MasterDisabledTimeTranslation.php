<?php
declare(strict_types=1);

namespace App\Models;

use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * App\Models\MasterDisabledTimeTranslation
 *
 * @property int $id
 * @property int $disabled_time_id
 * @property string $locale
 * @property string $title
 * @property string $description
 * @property MasterDisabledTime|null $masterDisabledTime
 * @method static Builder|self newModelQuery()
 * @method static Builder|self newQuery()
 * @method static Builder|self query()
 * @method static Builder|self whereId($value)
 * @method static Builder|self whereMasterDisabledTimeId($value)
 * @method static Builder|self whereTitle($value)
 * @mixin Eloquent
 */
class MasterDisabledTimeTranslation extends Model
{
    public $timestamps = false;

    protected $guarded = ['id'];

    public function masterDisabledTime(): BelongsTo
    {
        return $this->belongsTo(MasterDisabledTime::class);
    }
}
