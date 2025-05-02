<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Actions\Fortify\PasswordValidationRules;
use App\Actions\ListUserAction;
use App\Data\BreadcrumbItemData;
use App\Data\UserData;
use App\Http\Requests\FilterUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

final class UserController extends Controller
{
    public function index(FilterUserRequest $request, ListUserAction $action): Response
    {
        $this->withBreadcrumbs(
            BreadcrumbItemData::from(trans('navigation.users'), route('admin.users.index')),
        );

        return Inertia::render('User/Index', [
            'users' => $action->handle($request->validated()),
        ]);
    }

    public function edit(User $user): Response
    {
        $this->withBreadcrumbs(
            BreadcrumbItemData::from(trans('navigation.users'), route('admin.users.index')),
            BreadcrumbItemData::from($user->name),
        );

        return Inertia::render('User/Edit', ['user' => UserData::from($user)]);
    }

    use PasswordValidationRules;

    public function update(User $user, UpdateUserRequest $request): RedirectResponse
    {
        $user->fill($request->safe()->all())->save();

        return redirect()
            ->route('admin.users.index')
            ->banner(trans('user.updated'));
    }

    public function destroy(User $user): RedirectResponse
    {
        $user->delete();

        return redirect()->route('admin.users.index')->dangerBanner(trans('user.deleted'));
    }
}
