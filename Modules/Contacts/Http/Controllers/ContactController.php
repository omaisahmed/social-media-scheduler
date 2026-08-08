<?php

declare(strict_types=1);

namespace Modules\Contacts\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Modules\Contacts\Http\Requests\ImportContactsRequest;
use Modules\Contacts\Http\Requests\StoreContactRequest;
use Modules\Contacts\Http\Requests\StoreRemoteContactRequest;
use Modules\Contacts\Http\Requests\UpdateContactRequest;
use Modules\Contacts\Models\ContactHandle;
use Modules\Contacts\Repositories\Contracts\ContactRepositoryInterface;
use Modules\Contacts\Services\ContactImportService;
use Modules\Contacts\Services\ContactService;
use Modules\SocialAccounts\Repositories\Contracts\SocialAccountRepositoryInterface;

final class ContactController
{
    public function __construct(
        protected ContactService $contacts,
        protected ContactRepositoryInterface $repository,
        protected ContactImportService $importService,
        protected SocialAccountRepositoryInterface $accounts,
    ) {}

    public function index(Request $request): View
    {
        $filters = $request->only(['search']);

        return view('contacts::index', [
            'contacts' => $this->repository->paginateForBusiness($request->user()->business_id, $filters),
            'filters' => $filters,
            'platforms' => ContactHandle::PLATFORMS,
            'connectedAccounts' => $this->accounts->connectedForBusiness($request->user()->business_id),
        ]);
    }

    public function create(): View
    {
        return view('contacts::create', [
            'platforms' => ContactHandle::PLATFORMS,
        ]);
    }

    public function store(StoreContactRequest $request): RedirectResponse
    {
        $this->contacts->create(
            $request->user()->business_id,
            $request->safe()->except(['handles']),
            $request->input('handles', []),
        );

        return redirect()->route('contacts.index')->with('status', 'contact-created');
    }

    public function edit(Request $request, int $contactId): View
    {
        $contact = $this->repository->findForBusiness($request->user()->business_id, $contactId, ['handles']);

        abort_unless($contact, 404);

        return view('contacts::edit', [
            'contact' => $contact,
            'platforms' => ContactHandle::PLATFORMS,
        ]);
    }

    public function update(UpdateContactRequest $request, int $contactId): RedirectResponse
    {
        $contact = $this->repository->findForBusiness($request->user()->business_id, $contactId);

        abort_unless($contact, 404);

        $this->contacts->update(
            $request->user()->business_id,
            $contactId,
            $request->safe()->except(['handles']),
            $request->input('handles', []),
        );

        return redirect()->route('contacts.index')->with('status', 'contact-updated');
    }

    public function destroy(Request $request, int $contactId): RedirectResponse
    {
        $this->repository->deleteForBusiness($request->user()->business_id, $contactId);

        return redirect()->route('contacts.index')->with('status', 'contact-deleted');
    }

    /**
     * JSON autocomplete used by the post editor's @mention picker.
     */
    public function search(Request $request): JsonResponse
    {
        $query = trim((string) $request->query('q', ''));
        $businessId = $request->user()->business_id;

        $local = $this->repository->searchForBusiness($businessId, $query, 10)->map(fn ($contact) => [
            'id' => (string) $contact->getKey(),
            'value' => $contact->name,
            'name' => $contact->name,
            'handle' => $contact->primaryHandle()?->handleAt(),
            'avatar' => $contact->avatar_url,
        ]);

        $remote = $this->importService->search($businessId, $query, 6);

        return response()->json($local->concat($remote)->all());
    }

    /**
     * Import mentionable accounts (followers) from a connected account.
     */
    public function import(ImportContactsRequest $request): RedirectResponse
    {
        $businessId = $request->user()->business_id;
        $account = $this->accounts->findForBusiness($businessId, (int) $request->validated('account_id'));

        abort_unless($account, 404);

        $result = $this->importService->importFromAccount($businessId, $account);

        $status = $result['errors'] === []
            ? "Imported {$result['imported']} of {$result['total']} followers."
            : 'Import failed: '.implode(' ', $result['errors']);

        return redirect()->route('contacts.index')->with('status', $status);
    }

    /**
     * Persist a contact picked from live platform search, then let the
     * editor insert the mention with its new local id.
     */
    public function storeFromRemote(StoreRemoteContactRequest $request): JsonResponse
    {
        $data = $request->validated();

        $contact = $this->contacts->upsertFromPlatform(
            $request->user()->business_id,
            $data['platform'],
            [
                'name' => $data['name'],
                'handle' => $data['handle'] ?? null,
                'uid' => $data['platform_uid'],
                'avatar' => $data['avatar_url'] ?? null,
            ],
        );

        return response()->json([
            'id' => $contact?->getKey() !== null ? (string) $contact->getKey() : null,
            'value' => $contact?->name,
            'name' => $contact?->name,
            'handle' => $contact?->primaryHandle()?->handleAt(),
            'avatar' => $contact?->avatar_url,
        ]);
    }
}
