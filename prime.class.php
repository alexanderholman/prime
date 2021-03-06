<?php

namespace alexanderholman;

class prime {

    const DO_NOTHING = 0;

    const BUILD_PRIME_NUMBERS = 1;

    private $ConstructionJobs = [
        self::DO_NOTHING => '',
        self::BUILD_PRIME_NUMBERS => 'buildPrimeNumbers'
    ];

    private $PrimeNumbers = [ 2 ];
    
    private $PrimeNumberCount = 1;

    private $NonePrimeNumbers = [];

    private $NonePrimeNumberCount = 0;

    private $DividerNumbers = [];

    private $DividerCount = 0;

    private $CheckCount = 0;
    
    private $StartTime = null;
    
    private $EndTime = null;
    
    private static $Workers = [];
    
    private static $TimeLimit = 3600;
    
    private static $TestLimit = 10000;
    
    private static $StartingFrom = 3;

    private static $TestAgainstAllDividers = false;

    private function NumberIsDivisibleByDivider( int $Number, int $Divider ) : bool
    {
        return !( $Number%$Divider );
    }

    /**
     * @param int $Number
     * @return bool
     */
    private function isEven( int $Number ) : bool
    {
        return $this->NumberIsDivisibleByDivider( $Number, 2 );
    }

    /**
     * @param int $Number
     * @return bool
     */
    private function isOdd( int $Number ) : bool
    {
        return !$this->isEven( $Number );
    }

    private function getFirstXDividers( float $X ) : array
    {
        return array_slice( $this->DividerNumbers, 0, ceil( $this->DividerCount * $X ) );
    }

    private function getDividersFormNumber( int $Number ) : array
    {
        if ( !static::$TestAgainstAllDividers )
        {
            return $this->getFirstXDividers( $this->getPrimeCountMultiplier( $Number ) );
        }
        return $this->DividerNumbers;
    }

    public function getPrimeCountMultiplier( int $Number )
    {
        return 1 / ( ( strlen( $Number ) * strlen( $Number ) - 1 ) + 1 ) / ceil( strlen( $Number ) / 4 );
    }
    
    public function __construct( int $ConstructionJob = self::DO_NOTHING )
    {
        if ( isset( $this->ConstructionJobs[ $ConstructionJob ] ) && method_exists( $this, $this->ConstructionJobs[ $ConstructionJob ] ) ) call_user_func( array( $this, $this->ConstructionJobs[ $ConstructionJob ] ) );
    }

    public function isPrime( int $Number, bool $CheckKnown = false ) : bool
    {

        $TotalCount = $this->PrimeNumberCount + $this->NonePrimeNumberCount;
        if ( $CheckKnown || $Number <= $TotalCount )
        {
            foreach ( $this->PrimeNumbers as $PrimeNumber )
            {
                if ( $PrimeNumber === $Number ) return true;
            }
            if ( $Number <= $this->PrimeNumberCount + $this->NonePrimeNumberCount ) return false;
        }
        if ( $Number < 1 ) return false;
        if ( $Number > 2 && $this->isEven( $Number ) ) return false; # If $Number is even: Then $Number is divisible by 2 and therefore is not prime
        if ( $Number > 3 && $this->NumberIsDivisibleByDivider( $Number, 3 ) ) return false;
        if ( $Number > 5 && $this->NumberIsDivisibleByDivider( $Number, 5 ) ) return false;
        if ( $Number > 7 && $this->NumberIsDivisibleByDivider( $Number, 7 ) ) return false;
        if ( $Number > 9 && $this->NumberIsDivisibleByDivider( $Number, 9 ) ) return false;
        $Dividers = $this->getDividersFormNumber( $Number );
        if ( count( $Dividers ) )
        {
            foreach ( $Dividers as $Divider )
            {
                $this->CheckCount++;
                if ( $this->NumberIsDivisibleByDivider( $Number, $Divider ) ) return false;
            }
        }
        return true;
    }

    public function buildPrimeNumbers()
    {
        set_time_limit( static::$TimeLimit );
        $this->StartTime = microtime( true );
        for ($Number = static::$StartingFrom; $Number <= static::$TestLimit; $Number++ )
        {
            if ( $this->isPrime( $Number ) )
            {
                $this->PrimeNumberCount++;
                $this->PrimeNumbers[] = $Number;
                $this->DividerCount++;
                $this->DividerNumbers[] = $Number;
            }
            else
            {
                $this->NonePrimeNumberCount++;
                $this->NonePrimeNumbers = $Number;
            }
        }
        $this->EndTime = microtime( true );
    }

    public function getPrimeNumbers() : array
    {
        return $this->PrimeNumbers;
    }

    public function getNthPrimeNumber( $N ) : int
    {
        $i = $N - 1;
        return isset( $this->PrimeNumbers[ $i ] ) ? $this->PrimeNumbers[ $i ] : 0;
    }

    public function getPrimeNumberCount() : int
    {
        return $this->PrimeNumberCount;
    }

    public function getCheckCount() : int
    {
        return $this->CheckCount;
    }

    public function getLastBuildPrimeNumbersTime() : float
    {
        return $this->EndTime - $this->StartTime;
    }

    public static function getWorkers()
    {
        return static::$Workers;
    }

    public static function setWorkers( $Workers )
    {
        static::$Workers = $Workers;
    }

    public static function getTimeLimit() : int
    {
        return static::$TimeLimit;
    }

    public static function setTimeLimit( int $TimeLimit )
    {
        static::$TimeLimit = $TimeLimit;
    }

    public static function getTestLimit() : int
    {
        return static::$TestLimit;
    }

    public static function setTestLimit( int $TestLimit )
    {
        static::$TestLimit = $TestLimit;
    }

    public static function getStartingFrom() : int
    {
        return static::$StartingFrom;
    }

    public static function setStartingFrom( int $StartingFrom )
    {
        static::$StartingFrom = $StartingFrom;
    }

    public static function setTestAgainstAllDividers( bool $TestAgainstAllDividers )
    {
        static::$TestAgainstAllDividers = $TestAgainstAllDividers;
    }

}