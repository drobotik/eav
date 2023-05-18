<?php
/**
 * This file is part of the eav package.
 * @author    Aleksandr Drobotik <drobotiksbox@gmail.com>
 * @copyright 2023 Aleksandr Drobotik
 * @license   https://opensource.org/license/mit  The MIT License
 */
declare(strict_types=1);

namespace Drobotik\Eav\QueryBuilder;

use Drobotik\Eav\Enum\QB_CONFIG;
use Drobotik\Eav\Enum\QB_OPERATOR;

class QueryBuilderParser
{
    private QueryBuilderManager $manager;
    public function getManager() : QueryBuilderManager
    {
        return $this->manager;
    }
    public function setManager(QueryBuilderManager $manager): void
    {
        $this->manager = $manager;
    }
    public function parse(array $config, QueryBuilderGroup $group = null) : QueryBuilderGroup
    {
        if(is_null($group))
        {
            $group = new QueryBuilderGroup();
            $group->setAttributes($this->getManager()->getAttributesPivot());
        }
        if(key_exists(QB_CONFIG::CONDITION, $config))
        {
            $group->setCondition($config[QB_CONFIG::CONDITION]);
        }
        if (!key_exists(QB_CONFIG::RULES,  $config) || !is_array($config[QB_CONFIG::RULES]))
        {
            return $group;
        }
        foreach($config[QB_CONFIG::RULES] as $cfg)
        {
            if(key_exists(QB_CONFIG::RULES,  $cfg)) {
                $subGroup = $this->parse($cfg);
                $group->appendGroup($subGroup);
            } else {
                $rule = $this->makeRule($cfg);
                $group->appendRule($rule);
            }
        }
        return $group;
    }

    public function makeRule($config) : QueryBuilderRule
    {
        $rule = new QueryBuilderRule();
        $rule->setName($config[QB_CONFIG::NAME]);
        $operator = QB_OPERATOR::getCase($config[QB_CONFIG::OPERATOR]);
        $rule->setOperator($operator);
        $rule->setValue($config[QB_CONFIG::VALUE]);
        return $rule;
    }
}