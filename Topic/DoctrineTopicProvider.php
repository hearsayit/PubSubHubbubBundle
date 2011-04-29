<?php

/*
 * This file is part of the Hearsay PubSubHubbub bundle.
 *
 * The Hearsay PubSubHubbub bundle is free software: you can redistribute it
 * and/or modify it under the terms of the GNU Lesser General Public License
 * as published by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.
 *
 * The Hearsay PubSubHubbub bundle is distributed in the hope that it will be
 * useful, but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with the Hearsay PubSubHubbub bundle.  If not, see
 * <http://www.gnu.org/licenses/>.
 */

namespace Hearsay\PubSubHubbubBundle\Topic;

use Doctrine\ORM\EntityManager;
use Hearsay\PubSubHubbubBundle\Exception\NonUniqueIdException;

/**
 * Description of DoctrineTopicProvider
 *
 * @author kevin
 */
class DoctrineTopicProvider implements TopicProviderInterface {

    /**
     * The entity manager used to retrieve topics.
     * @var EntityManager
     */
    private $entityManager = null;
    /**
     * The class of topic entites.
     * @var string
     */
    private $topicClass = null;
    /**
     * The property of topic entities which should be used as a unique
     * identifier.
     * @var string
     */
    private $topicIdProperty = null;

    public function __construct(EntityManager $entityManager, $topicClass, $topicIdProperty = 'id') {
        $this->entityManager = $entityManager;
    }

    /**
     * Get the entity manager used to retrieve topics.
     * @return EntityManager The entity manager.
     */
    protected function getEntityManager() {
        return $this->entityManager;
    }

    /**
     * Get the entity class of topics in the database.
     * @return string The fully-qualified class name.
     */
    protected function getTopicClass() {
        return $this->topicClass;
    }

    /**
     * Get the property of topic entities which should be used as a unique
     * identifier.  Must be returned by the getTopicId() method of the entites.
     * @return string The property.
     */
    protected function getTopicIdProperty() {
        return $this->topicIdProperty;
    }

    /**
     * {@inheritdoc}
     */
    public function getTopic($topicId) {
        $repo = $this->getEntityManager()->getRepository($this->getTopicClass());
        $results = $repo->findBy(array(
                    $this->getTopicIdProperty() => $topicId,
                ));
        if (\count($results) > 1) {
            throw new NonUniqueIdException('Multiple topics found with id: ' . $topicId);
        } else if (\count($results) === 0) {
            return null;
        } else {
            return $results[0];
        }
    }

}
