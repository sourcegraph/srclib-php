<?php

namespace Sourcegraph\PHP;

use Sourcegraph\PHP\SourceUnit;


class DepResolver
{
    public function run(SourceUnit $unit)
    {
        $result = [];
        foreach ($unit->getDependencies() as $package) {
            $result[] = [
                'Raw' => $package,
                'Target' => $this->createTarget($unit, $package)
            ];
        }

        return $result;
    }

    protected function createTarget(SourceUnit $unit, $package)
    {
        return [
            'ToRepoCloneURL' => $unit->getRepository($package),
            'ToUnit' => $package,
            'ToUnitType' => $unit->getType(),
            'ToVersionString' => $unit->getRequiredVersion($package),
            'ToRevSpec' => $unit->getCommit($package)
        ];
    }
}
