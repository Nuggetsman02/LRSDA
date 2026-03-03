<?php

namespace LRSDA\Server\Services;

use LRSDA\Server\Services\XapiRegistry; 


class QueryService
{
    private XapiRegistry $registry;

    public function __construct()
    {
        // On initialise le registre ici pour faire les traductions
        $this->registry = XapiRegistry::getInstance();
    }

    // --------
    // METHODES PUBLIQUES
    // --------

    /**
     * Construit le filtre MongoDB pour la Connection API à partir des filtres bruts.
     */
    public function buildMongoFilter(array $rawFilters): array
    {
        $mongoFilter = [];

        // Filtre par Date
        if (!empty($rawFilters['start_date']) && !empty($rawFilters['end_date'])) {
            $mongoFilter['statement.stored'] = [
                '$gte' => $rawFilters['start_date'] . 'T00:00:00Z',
                '$lte' => $rawFilters['end_date'] . 'T23:59:59Z'
            ];
        } elseif (!empty($rawFilters['start_date'])) {
             $mongoFilter['statement.stored']['$gte'] = $rawFilters['start_date'] . 'T00:00:00Z';
        }

        // Filtre par Verbe
        if (!empty($rawFilters['verbs']) && is_array($rawFilters['verbs'])) {
            $verbIds = [];
            foreach ($rawFilters['verbs'] as $verbName) {
                $verbInfo = $this->registry->getVerb($verbName, true);
                if ($verbInfo && isset($verbInfo['id'])) {
                    $verbIds[] = $verbInfo['id'];
                }
            }

            if (!empty($verbIds)) {
                $mongoFilter['statement.verb.id'] = count($verbIds) === 1 ? $verbIds[0] : ['$in' => $verbIds];
            }
        }

        // Filtre par Activité
        if (!empty($rawFilters['objects']) && is_array($rawFilters['objects'])) {
            $typeUris = [];
            foreach ($rawFilters['objects'] as $objectName) {
                $uri = $this->registry->get($objectName, 'type'); 
                if ($uri) {
                    $typeUris[] = $uri;
                }
            }

            if (!empty($typeUris)) {
                $mongoFilter['statement.object.definition.type'] = count($typeUris) === 1 ? $typeUris[0] : ['$in' => $typeUris];
            }
        }

        return $mongoFilter;
    }
}