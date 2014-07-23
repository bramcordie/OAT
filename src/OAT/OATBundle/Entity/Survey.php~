<?php

namespace OAT\OATBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * An assessment survey.
 *
 * @ORM\Entity
 */
class Survey
{

    /**
     * id
     *
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    //* Naam (mag ook anomien) ; veld niet verplicht
    /**
     * student name
     *
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $studentName;

    //* Naam van de school (Sec. Onderwijs) ; verplicht veld
    /**
     * school name
     *
     * @ORM\Column(type="string", length=255)
     */
    protected $schoolName;

    //* Opleiding : rolmenu (ASO, TSO, BSO, KSO, andere: ....)
    /**
     * education type
     *
     * @ORM\Column(type="string", length=255)
     */
    protected $educationType;

    //* Studierichting: rolmenu  (hiervoor voeg ik een lijst toe aan de mail uit de gegevens van onze eerstejaarsstudenten om hiervoor een rolmenu te maken)
    /**
     * education
     *
     * @ORM\Column(type="string", length=255)
     */
    protected $education;

    //* Aantal uren wiskunde laatste jaar secundair onderwijs: invulveld (getalwaarde)
    /**
     * hours of math
     *
     * @ORM\Column(type="integer")
     * @Assert\Max(limit = 50, message = "Het aantal uren kan niet meer zijn dan 50.")
     * @Assert\Min(limit = 0, message = "Het aantal uren moet positief zijn.")
     */
    protected $hoursMaths;

    //* Aantal uren wetenschappen laatste jaar secundair onderwijs (Natuurwetenschappen of de som van Bio, Chemie, Fysica): invulveld (getalwaarde)
    /**
     * hours of science
     *
     * @ORM\Column(type="integer")
     * @Assert\Max(limit = 50, message = "Het aantal uren kan niet meer zijn dan 50.")
     * @Assert\Min(limit = 0, message = "Het aantal uren moet positief zijn.")
     */
    protected $hoursScience;

    //* Voor welke opleiding wens je je in te schrijven? : rolmenu (Biomedische Laboratoriumtechnologie, Chemie, Voedings- en dieetkunde, ik weet het nog niet)
    /**
     * future education
     *
     * @ORM\Column(type="string", length=255)
     */
    protected $futureEducation;

    //* In welk academiejaar wens je te starten aan de KHLeuven? (2012-2013, 2013-2014, 2014-2015, ik weet het nog niet).
    /**
     * starting year
     *
     * @ORM\Column(type="integer")
     * @Assert\Min(limit = 2012, message = "het academiejaar moet in de toekomst liggen.")
     * @Assert\Max(limit = 10000)
     */
    protected $startingYear;

    /**
     * assessment
     *
     * @ORM\ManyToOne(targetEntity="Assessment")
     */
    protected $assessment;

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set studentName
     *
     * @param string $studentName
     */
    public function setStudentName($studentName)
    {
        $this->studentName = $studentName;
    }

    /**
     * Get studentName
     *
     * @return string
     */
    public function getStudentName()
    {
        return $this->studentName;
    }

    /**
     * Set schoolName
     *
     * @param string $schoolName
     */
    public function setSchoolName($schoolName)
    {
        $this->schoolName = $schoolName;
    }

    /**
     * Get schoolName
     *
     * @return string
     */
    public function getSchoolName()
    {
        return $this->schoolName;
    }

    /**
     * Set educationType
     *
     * @param string $educationType
     */
    public function setEducationType($educationType)
    {
        $this->educationType = $educationType;
    }

    /**
     * Get educationType
     *
     * @return string
     */
    public function getEducationType()
    {
        return $this->educationType;
    }

    /**
     * Set education
     *
     * @param string $education
     */
    public function setEducation($education)
    {
        $this->education = $education;
    }

    /**
     * Get education
     *
     * @return string
     */
    public function getEducation()
    {
        return $this->education;
    }

    /**
     * Set hoursMaths
     *
     * @param integer $hoursMaths
     */
    public function setHoursMaths($hoursMaths)
    {
        $this->hoursMaths = $hoursMaths;
    }

    /**
     * Get hoursMaths
     *
     * @return integer
     */
    public function getHoursMaths()
    {
        return $this->hoursMaths;
    }

    /**
     * Set hoursScience
     *
     * @param integer $hoursScience
     */
    public function setHoursScience($hoursScience)
    {
        $this->hoursScience = $hoursScience;
    }

    /**
     * Get hoursScience
     *
     * @return integer
     */
    public function getHoursScience()
    {
        return $this->hoursScience;
    }

    /**
     * Set futureEduction
     *
     * @param string $futureEduction
     */
    public function setFutureEduction($futureEduction)
    {
        $this->futureEduction = $futureEduction;
    }

    /**
     * Get futureEduction
     *
     * @return string
     */
    public function getFutureEduction()
    {
        return $this->futureEduction;
    }

    /**
     * Set startingYear
     *
     * @param integer $startingYear
     */
    public function setStartingYear($startingYear)
    {
        $this->startingYear = $startingYear;
    }

    /**
     * Get startingYear
     *
     * @return integer
     */
    public function getStartingYear()
    {
        return $this->startingYear;
    }

    /**
     * Set assessment
     *
     * @param OAT\OATBundle\Entity\Assessment $assessment
     */
    public function setAsssessment(\OAT\OATBundle\Entity\Assessment $assessment)
    {
        $this->assessment = $assessment;
    }

    /**
     * Get assessment
     *
     * @return OAT\OATBundle\Entity\Assessment
     */
    public function getAssessment()
    {
        return $this->assessment;
    }

    /**
     * Set futureEducation
     *
     * @param string $futureEducation
     */
    public function setFutureEducation($futureEducation)
    {
        $this->futureEducation = $futureEducation;
    }

    /**
     * Get futureEducation
     *
     * @return string
     */
    public function getFutureEducation()
    {
        return $this->futureEducation;
    }

    /**
     * Set assessment
     *
     * @param OAT\OATBundle\Entity\Assessment $assessment
     */
    public function setAssessment(\OAT\OATBundle\Entity\Assessment $assessment)
    {
        $this->assessment = $assessment;
    }
}