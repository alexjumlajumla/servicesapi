<?php
declare(strict_types=1);

namespace App\Models;

use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\ServiceExtraTranslation
 *
 * @property int $id
 * @property int $service_extra_id
 * @property string $locale
 * @property string $title
 * @property string|null $description
 * @method static Builder|self newModelQuery()
 * @method static Builder|self newQuery()
 * @method static Builder|self query()
 * @method static Builder|self whereDescription($value)
 * @method static Builder|self whereId($value)
 * @method static Builder|self whereLocale($value)
 * @method static Builder|self whereServiceExtraId($value)
 * @method static Builder|self whereTitle($value)
 * @mixin Eloquent
 */
class ServiceExtraTranslation extends Model
{
    protected $guarded = ['id'];

    public $timestamps = false;
}
