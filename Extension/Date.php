<?php

namespace Cornichon\ForumBundle\Extension;

use Symfony\Component\DependencyInjection\ContainerInterface;

class Date extends \Twig_Extension {

    protected $container;

    public function __construct (ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function getFilters() 
    {
        return array(
            "timeSince" => new \Twig_Filter_Method($this, 'timeSince')
        );
    }

    /**
     * Provides a string that gives a better idea of a time period
     * Example:
     *   - 1 hour and 2 minutes ago
     *   - 4 days and 4 seconds ago
     *
     * @param DateTime $date
     * @param integer  $maxDigit = 2    The number of digits
     * @param string   $glue = ", "     The separator
     *
     * @return string
     */
    public function timeSince(\DateTime $date, $maxDigit = 2, $glue = ", ")
    {
        $i = 0;
        $array = array();

        /**
         * We need a time period since the given data
         * so we substract the current to it
         */
        $dateInterval = $date->diff(new \DateTime());

        $array = array();

        // More than a year ago?
        if ($dateInterval->y > 0) {
            $array[] = $dateInterval->y ." years";
            $i++;
        }

        // More than a month ago?
        if ($i < $maxDigit && $dateInterval->m > 0) {
            $array[] = $dateInterval->m ." months";
            $i++;
        }

        // More than a day ago?
        if ($i < $maxDigit && $dateInterval->d > 0) {
            $array[] = $dateInterval->d ." days";
            $i++;
        }

        // More than an hour ago?
        if ($i < $maxDigit && $dateInterval->h > 0) {
            $array[] = $dateInterval->h ." hours";
            $i++;
        }

        // More than a minute ago?
        if ($i < $maxDigit && $dateInterval->i > 0) {
            $array[] = $dateInterval->i ." minutes";
            $i++;
        }

        // More than a second ago?
        if ($i < $maxDigit && $dateInterval->s >= 0) {
            $array[] = $dateInterval->s ." seconds";
        }

        return implode($glue, $array) ." ago";
    }

    public function getName() 
    {
        return "Date_Extension";
    }

}