<?php

namespace App\Http\Controllers;

use App\Http\Requests\AddressCreateRequest;
use App\Http\Requests\AddressUpdateRequest;
use App\Http\Resources\AddressResource;
use App\Models\Address;
use App\Models\Contact;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AddressController extends Controller
{
    private function getContact(int $idContact): Contact
    {
        $user = Auth::user();
        $contact = $user->contacts()->find($idContact);

        if (!$contact) {
            abort(404, 'Contact not found');
        }

        return $contact;
    }

    private function getAddress(Contact $contact, int $idAddress): Address
    {
        $address = $contact->addresses()->find($idAddress);

        if (!$address) {
            abort(404, 'Address not found');
        }

        return $address;
    }

    public function create(int $idContact, AddressCreateRequest $request): JsonResponse
    {
        $contact = $this->getContact($idContact);

        $data = $request->validated();
        $address = $contact->addresses()->create($data);

        return response()->json(new AddressResource($address), 201);
    }

    public function get(int $idContact, int $idAddress): AddressResource
    {
        $contact = $this->getContact($idContact);
        $address = $this->getAddress($contact, $idAddress);

        return new AddressResource($address);
    }

    public function update(int $idContact, int $idAddress, AddressUpdateRequest $request): AddressResource
    {
        $contact = $this->getContact($idContact);
        $address = $this->getAddress($contact, $idAddress);

        $data = $request->validated();
        $address->update($data);

        return new AddressResource($address);
    }

    public function delete(int $idContact, int $idAddress): JsonResponse
    {
        $contact = $this->getContact($idContact);
        $address = $this->getAddress($contact, $idAddress);
        $address->delete();

        return response()->json(['data' => true]);
    }

    public function list(int $idContact): JsonResponse
    {
        $contact = $this->getContact($idContact);
        $addresses = $contact->addresses;

        return response()->json(AddressResource::collection($addresses), 200);
    }
}
