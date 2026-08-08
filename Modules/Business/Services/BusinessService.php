<?php

declare(strict_types=1);

namespace Modules\Business\Services;

use Illuminate\Support\Str;
use Modules\Business\Models\Business;
use Modules\Business\Repositories\Contracts\BusinessRepositoryInterface;

final class BusinessService
{
    public function __construct(protected BusinessRepositoryInterface $businesses)
    {
    }

    public function create(array $data, int $ownerId): Business
    {
        $data['slug'] = $data['slug'] ?? Str::slug($data['name']);

        $business = $this->businesses->create($data);

        $owner = \App\Models\User::findOrFail($ownerId);
        $owner->business_id = $business->getKey();
        $owner->save();

        return $business;
    }

    public function update(int $id, array $data): Business
    {
        if (isset($data['name']) && empty($data['slug'])) {
            $data['slug'] = Str::slug($data['name']);
        }

        return $this->businesses->update($id, $data);
    }

    public function delete(int $id): bool
    {
        return $this->businesses->delete($id);
    }
}
