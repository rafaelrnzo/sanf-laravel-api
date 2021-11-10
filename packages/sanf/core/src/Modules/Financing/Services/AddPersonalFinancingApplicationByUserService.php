<?php


namespace Sanf\Core\Modules\Financing\Services;


use NbsPhp\Core\Services\ApplicationServiceInterface;
use Sanf\Core\Modules\Financing\Dto\AddFinancingApplicationDto;
use Sanf\Core\Modules\Financing\Dto\FinancingObjectDto;
use Sanf\Core\Modules\Financing\Enums\FinancingApplicationTypeEnum;
use Sanf\Core\Modules\Financing\Enums\FinancingStatusEnum;
use Sanf\Core\Modules\Financing\Events\FinancingApplicationCreatedEvent;
use Sanf\Core\Modules\Financing\Exceptions\FinancingApplicationInvalidException;
use Sanf\Core\Modules\User\Enums\ProfileType;

class AddPersonalFinancingApplicationByUserService extends FinancingByUserService implements ApplicationServiceInterface
{
    /**
     * @param AddFinancingApplicationDto $dto
     * @return mixed
     * @throws \NbsPhp\Core\Exceptions\UserNotFoundException
     */
    public function execute($dto = null)
    {
        $user = $this->findUserOrFail($dto->userId);
        if ($dto->profile->typeId !== ProfileType::PERSONAL) {
            throw new FinancingApplicationInvalidException('Invalid Profile Type');
        }

        //TODO VALIDATE OPTION, facility, method and BTM

        /**@var FinancingObjectDto * */
        $financingObjects = [];
        foreach ($dto->financingObjects as $financingObject) {
            $financingObjects[] = [
                'amount' => $financingObject->amount,
                'provider_name' => $financingObject->providerName,
                'brand_id' => $financingObject->brandId,
                'brand_name' => $financingObject->brandName,
                'type_id' => $financingObject->typeId,
                'type_name' => $financingObject->typeName,
                'model_id' => $financingObject->modelId,
                'model_name' => $financingObject->modelName,
            ];
        }
        $profileSnapshot = [
            'xid' => $dto->profile->xid,
            'type_id' => $dto->profile->typeId,
            'type_name' => $dto->profile->typeName,
            'title' => $dto->profile->title,
            'full_name' => $dto->profile->fullName,
            'pic_name' => $dto->profile->picName,
            'identity_number' => $dto->profile->identityNumber,
            'npwp' => $dto->profile->npwp,
            'email' => $dto->profile->email,
            'landline_number' => $dto->profile->landlineNumber,
            'phone_number' => $dto->profile->phoneNumber,
            'gender' => $dto->profile->gender,
            'birthdate' => $dto->profile->birthdate,
            'country_id' => $dto->profile->countryId,
            'country_name' => $dto->profile->countryName,
            'province_id' => $dto->profile->provinceId,
            'province_name' => $dto->profile->provinceName,
            'city_id' => $dto->profile->cityId,
            'city_name' => $dto->profile->cityName,
            'district_name' => $dto->profile->districtName,
            'subdistrict_name' => $dto->profile->subdistrictName,
            'postcode' => $dto->profile->postcode,
            'address' => $dto->profile->address,
            'business_since' => $dto->profile->businessSince,
            'is_pic' => $dto->profile->isPic,
            'version' => 1
        ];
        $newFinancingApplication = $this->financingApplicationRepository->add([
            'xid' => nano_id(),
            'application_code' => $this->generateApplicationCode(),
            'user_id' => $user->id,
            'profile_xid' => $dto->profileXid,
            'profile_snapshot' => $profileSnapshot,
            'facility_id' => $dto->financingFacilityId,
            'method_id' => $dto->financingMethodId,
            'financing_objects' => $financingObjects,
            'is_receive_offer' => $dto->isReceiveOffer,
            'segment' => $dto->segment,
            'project_location' => $dto->projectLocation,
            'status_id' => FinancingStatusEnum::PROCESSED,
            'type_id' => FinancingApplicationTypeEnum::PERSONAL
        ]);

        $financingApplication = $this->financingApplicationRepository->findById($newFinancingApplication->id);
        $financingApplication->profile = $dto->profile;
        event(new FinancingApplicationCreatedEvent($financingApplication));
        return $financingApplication;
    }
}
