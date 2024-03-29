<?php

namespace App\Http\Controllers;

use App\Http\Requests\ContactCreateRequest;
use App\Http\Requests\ContactUpdateRequest;
use App\Http\Resources\ContactCollection;
use App\Http\Resources\ContactResource;
use App\Models\Contact;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ContactController extends Controller
{
    public function create(ContactCreateRequest $request): JsonResponse
    {
        $data = $request->validated();
        $user = Auth::user();

        $contact = new Contact($data);
        $contact->user_id = $user->id;
        $contact->save();

        return response()->json(new ContactResource($contact), 201);
    }

    public function get(int $id): ContactResource
    {
        $user = Auth::user();
        $contact = $user->contacts()->find($id);

        if (!$contact) {
            abort(404, 'Contact not found');
        }

        return new ContactResource($contact);
    }

    public function update(int $id, ContactUpdateRequest $request): ContactResource
    {
        $user = Auth::user();
        $contact = $user->contacts()->find($id);

        if (!$contact) {
            abort(404, 'Contact not found');
        }

        $data = $request->validated();
        $contact->update($data);

        return new ContactResource($contact);
    }

    public function delete(int $id): JsonResponse
    {
        $user = Auth::user();
        $contact = $user->contacts()->find($id);

        if (!$contact) {
            abort(404, 'Contact not found');
        }

        $contact->delete();

        return response()->json(['data' => true]);
    }

    public function search(Request $request): ContactCollection
    {
        $user = Auth::user();
        $page = $request->input('page', 1);
        $size = $request->input('size', 10);
        $name = $request->input('name');
        $email = $request->input('email');
        $phone = $request->input('phone');

        $query = $user->contacts();

        if ($name) {
            $query->where(function ($query) use ($name) {
                $query->where('first_name', 'like', "%$name%")
                    ->orWhere('last_name', 'like', "%$name%");
            });
        }

        if ($email) {
            $query->where('email', 'like', "%$email%");
        }

        if ($phone) {
            $query->where('phone', 'like', "%$phone%");
        }

        $contacts = $query->paginate($size, ['*'], 'page', $page);

        return new ContactCollection($contacts);
    }
}
