<?php

declare(strict_types=1);

namespace Modules\Core\Models;

use Illuminate\Database\Eloquent\Model;
use Modules\Core\Traits\HasAudit;
use Modules\Core\Traits\HasBusiness;

/**
 * Shared base model for every domain entity.
 *
 * Provides business scoping and audit tracking so child models stay thin
 * and free of cross-cutting concerns.
 */
abstract class BaseModel extends Model
{
    use HasAudit;
    use HasBusiness;
}
