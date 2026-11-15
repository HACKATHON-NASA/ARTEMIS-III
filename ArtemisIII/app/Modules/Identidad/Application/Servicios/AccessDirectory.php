<?php

declare(strict_types=1);

namespace App\Modules\Identidad\Application\Servicios;

use App\Modules\Entorno\Contratos\CrewMember;
use App\Modules\Entorno\Contratos\CrewQuery;
use App\Modules\Entorno\Contratos\MissionQuery;
use App\Modules\Identidad\Application\DTOs\ManagedDeviceResult;
use App\Modules\Identidad\Application\DTOs\ManagedUserResult;
use App\Modules\Identidad\Domain\Entidades\DeviceAccount;
use App\Modules\Identidad\Domain\Entidades\UserAccount;

/**
 * Pone a usuarios y dispositivos los nombres que viven en M6 (tripulante,
 * cargo y misión), preguntando por sus contratos una vez por lista.
 */
final readonly class AccessDirectory
{
    public function __construct(
        private CrewQuery $crew,
        private MissionQuery $missions,
    ) {}

    /**
     * @param  list<UserAccount>  $accounts
     * @return list<ManagedUserResult>
     */
    public function users(array $accounts): array
    {
        $crew = $this->crewById();
        $missions = $this->missions->names();

        return array_map(function (UserAccount $account) use ($crew, $missions): ManagedUserResult {
            $member = $account->astronautId === null ? null : ($crew[$account->astronautId] ?? null);
            $missionId = $member->missionId ?? $account->missionId;

            return new ManagedUserResult(
                account: $account,
                astronautName: $member?->name,
                crewRole: $member?->crewRole,
                missionId: $missionId,
                missionName: $missionId === null ? null : ($missions[$missionId] ?? null),
            );
        }, $accounts);
    }

    public function user(UserAccount $account): ManagedUserResult
    {
        return $this->users([$account])[0];
    }

    /**
     * @param  list<DeviceAccount>  $devices
     * @return list<ManagedDeviceResult>
     */
    public function devices(array $devices): array
    {
        $crew = $this->crewById();

        return array_map(
            fn (DeviceAccount $device): ManagedDeviceResult => new ManagedDeviceResult(
                device: $device,
                astronautName: $device->astronautId === null ? null : ($crew[$device->astronautId]->name ?? null),
            ),
            $devices,
        );
    }

    public function device(DeviceAccount $device): ManagedDeviceResult
    {
        return $this->devices([$device])[0];
    }

    /**
     * @return array<int, CrewMember>
     */
    private function crewById(): array
    {
        $byId = [];

        foreach ($this->crew->all() as $member) {
            $byId[$member->id] = $member;
        }

        return $byId;
    }
}
