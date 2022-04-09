<?php
// src/Twig/AppExtension.php
namespace App\Twig;

use App\Entity\Fields;
use Doctrine\ORM\EntityManager;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;
use App\Entity\User;

class AppExtension extends AbstractExtension
{
    private $em;

    public function __construct(EntityManager $em) {
        $this->em = $em;
    }

    //FILTERS
    public function getFilters()
    {
        return array(
            new TwigFilter('cast_to_array', array($this, 'objectFilter')),
        );
    }

    public function objectFilter($obj) {
        $array = [];
        foreach ((array)$obj as $key => $value) {
            $key = str_replace(User::class, '', $key);
            $array[$key] = $value;
        }
        return $array;
    }

    //FUNCTIONS
    public function getFunctions()
    {
        return [
            new TwigFunction('getField', [$this, 'getField']),
            new TwigFunction('isType', [$this, 'isType']),
            new TwigFunction('getRandomInt', [$this, 'getRandomInt']),
        ];
    }

    public function getField($field)
    {
        $fields_repo = $this->em->getRepository(Fields::class);
        return $fields_repo->findOneBy(['name' => $field])->getValue();
    }

    public function isType($value, $type)
    {
        return gettype($value) === $type;
    }

    public function getRandomInt($min, $max) {
        return random_int($min, $max);
    }
}