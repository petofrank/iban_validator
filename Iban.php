<?php

/**
 * Class Iban
 */
class Iban
{
    private const MODULO = '97';

    /**
     * Characters to numbers mapping used for modulo 97 calculation
     */
    private const CHARS = [
        'A'=>10, 'B'=>11, 'C'=>12, 'D'=>13, 'E'=>14, 'F'=>15, 'G'=>16,
        'H'=>17, 'I'=>18, 'J'=>19, 'K'=>20, 'L'=>21, 'M'=>22, 'N'=>23,
        'O'=>24, 'P'=>25, 'Q'=>26, 'R'=>27, 'S'=>28, 'T'=>29, 'U'=>30,
        'V'=>31, 'W'=>32, 'X'=>33, 'Y'=>34, 'Z'=>35
    ];

    /**
     * Note that this is not a complete list of all the countries in the world that supports IBAN
     *
     * Country:
     * Friendly representation of country
     *
     * Alpha2 code:
     * 2 digit representation of country
     *
     * Alpha3 code:
     * 3 digit representation of country
     *
     * Numeric:
     * Numeric representation of country
     *
     * Length:
     * specifies IBAN length
     *
     * Sepa:
     * Boolean flag indicating whether SEPA payments are enabled for country
     *
     * Format:
     * Specific IBAN format
     * k = IBAN check digit
     * b = National bank code
     * s = Branch code
     * p = Account number prefix
     * c = Account number
     * x = Account check digits
     * 0 = Zeroes
     * m = Currency code
     * t = Account type (cheque account, savings account etc.)
     * n = Owner account number ("1", "2" etc.)
     * q = BIC bank code
     *
     * https://en.wikipedia.org/wiki/International_Bank_Account_Number
     * https://www.iban.com/structure
     * https://www.iban.com/country-codes
     */
    private const IBAN_COUNTRY_MAP = [
        'AL' => ['country' => 'Albania', 'alpha2_code' => 'AL', 'alpha3_code' => 'ALB', 'numeric' => '004', 'sepa' => false, 'format' => 'ALkk bbbs sssx cccc cccc cccc cccc'],
        'AD' => ['country' => 'Andorra', 'alpha2_code' => 'AD', 'alpha3_code' => 'AND', 'numeric' => '020', 'sepa' => true, 'format' => 'ADkk bbbb ssss cccc cccc cccc'],
        'AT' => ['country' => 'Austria', 'alpha2_code' => 'AT', 'alpha3_code' => 'AUT', 'numeric' => '040', 'sepa' => true, 'format' => 'ATkk bbbb bccc cccc cccc'],
        'AZ' => ['country' => 'Azerbaijan', 'alpha2_code' => 'AZ', 'alpha3_code' => 'AZE', 'numeric' => '031', 'sepa' => false, 'format' => 'AZkk bbbb cccc cccc cccc cccc cccc'],
        'BH' => ['country' => 'Bahrain', 'alpha2_code' => 'BH', 'alpha3_code' => 'BHR', 'numeric' => '041', 'sepa' => false, 'format' => 'BHkk bbbb cccc cccc cccc cc'],
        'BY' => ['country' => 'Belarus', 'alpha2_code' => 'BY', 'alpha3_code' => 'BLR', 'numeric' => '112', 'sepa' => false, 'format' => 'BYkk bbbb aaaa cccc cccc cccc cccc'],
        'BE' => ['country' => 'Belgium', 'alpha2_code' => 'BE', 'alpha3_code' => 'BEL', 'numeric' => '056', 'sepa' => true, 'format' => 'BEkk bbbc cccc ccxx'],
        'BA' => ['country' => 'Bosnia and Herzegovina', 'alpha2_code' => 'BA', 'alpha3_code' => 'BIH', 'numeric' => '070', 'sepa' => false, 'format' => 'BAkk bbbs sscc cccc ccxx'],
        'BR' => ['country' => 'Brazil', 'alpha2_code' => 'BR', 'alpha3_code' => 'BRA', 'numeric' => '076', 'sepa' => false, 'format' => 'BRkk bbbb bbbb ssss sccc cccc ccct n'],
        'BG' => ['country' => 'Bulgaria', 'alpha2_code' => 'BG', 'alpha3_code' => 'BGR', 'numeric' => '100', 'sepa' => true, 'format' => 'BGkk bbbb ssss ttcc cccc cc'],
        'HR' => ['country' => 'Croatia', 'alpha2_code' => 'HR', 'alpha3_code' => 'HRV', 'numeric' => '191', 'sepa' => true, 'format' => 'HRkk bbbb bbbc cccc cccc c'],
        'CY' => ['country' => 'Cyprus', 'alpha2_code' => 'CY', 'alpha3_code' => 'CYP', 'numeric' => '196', 'sepa' => true, 'format' => 'CYkk bbbs ssss cccc cccc cccc cccc'],
        'CZ' => ['country' => 'Czech Republic', 'alpha2_code' => 'CZ', 'alpha3_code' => 'CZE', 'numeric' => '203', 'sepa' => true, 'format' => 'CZkk bbbb pppp sscc cccc cccc'],
        'CR' => ['country' => 'Costa Rica', 'alpha2_code' => 'CR', 'alpha3_code' => 'CRI', 'numeric' => '188', 'sepa' => false, 'format' => 'CRkk 0bbb cccc cccc cccc cc'],
        'DK' => ['country' => 'Denmark', 'alpha2_code' => 'DK', 'alpha3_code' => 'DNK', 'numeric' => '208', 'sepa' => true, 'format' => 'DKkk bbbb cccc cccc cx'],
        'DO' => ['country' => 'Dominican Republic', 'alpha2_code' => 'DO', 'alpha3_code' => 'DOM', 'numeric' => '214', 'sepa' => false, 'format' => 'DOkk bbbb cccc cccc cccc cccc cccc'],
        'TL' => ['country' => 'East Timor', 'alpha2_code' => 'TL', 'alpha3_code' => 'TLS', 'numeric' => '626', 'sepa' => false, 'format' => 'TLkk bbbc cccc cccc cccc cxx'],
        'EG' => ['country' => 'Egypt', 'alpha2_code' => 'EG', 'alpha3_code' => 'EGY', 'numeric' => '818', 'sepa' => false, 'format' => 'EGkk bbbb ssss cccc cccc cccc cccc c'],
        'SV' => ['country' => 'El Salvador', 'alpha2_code' => 'SV', 'alpha3_code' => 'SLV', 'numeric' => '222', 'sepa' => false, 'format' => 'SVkk bbbb cccc cccc cccc cccc cccc'],
        'EE' => ['country' => 'Estonia', 'alpha2_code' => 'EE', 'alpha3_code' => 'EST', 'numeric' => '233', 'sepa' => true, 'format' => 'EEkk bbss cccc cccc cccx'],
        'FO' => ['country' => 'Faroe Islands', 'alpha2_code' => 'FO', 'alpha3_code' => 'FRO', 'numeric' => '234', 'sepa' => false,  'format' => 'FOkk bbbb cccc cccc cx'],
        'FI' => ['country' => 'Finland', 'alpha2_code' => 'FI', 'alpha3_code' => 'FIN', 'numeric' => '246', 'sepa' => true,  'format' => 'FIkk bbbb bbcc cccc cx'],
        'FR' => ['country' => 'France', 'alpha2_code' => 'FR', 'alpha3_code' => 'FRA', 'numeric' => '250', 'sepa' => true,  'format' => 'FRkk bbbb bsss sscc cccc cccc cxx'],
        'GE' => ['country' => 'Georgia', 'alpha2_code' => 'GE', 'alpha3_code' => 'GEO', 'numeric' => '268', 'sepa' => false,  'format' => 'GEkk bbcc cccc cccc cccc cc'],
        'DE' => ['country' => 'Germany', 'alpha2_code' => 'DE', 'alpha3_code' => 'DEU', 'numeric' => '276', 'sepa' => true,  'format' => 'DEkk bbbb bbbb cccc cccc cc'],
        'GI' => ['country' => 'Gibraltar', 'alpha2_code' => 'GI', 'alpha3_code' => 'GIB', 'numeric' => '292', 'sepa' => true,  'format' => 'GIkk bbbb cccc cccc cccc ccc'],
        'GR' => ['country' => 'Greece', 'alpha2_code' => 'GR', 'alpha3_code' => 'GRC', 'numeric' => '300', 'sepa' => true,  'format' => 'GRkk bbbs sssc cccc cccc cccc ccc'],
        'GL' => ['country' => 'Greenland', 'alpha2_code' => 'GL', 'alpha3_code' => 'GRL', 'numeric' => '304', 'sepa' => false,  'format' => 'GLkk bbbb cccc cccc cx'],
        'GT' => ['country' => 'Guatemala', 'alpha2_code' => 'GT', 'alpha3_code' => 'GTM', 'numeric' => '320', 'sepa' => false,  'format' => 'GTkk bbbb mmtt cccc cccc cccc cccc'],
        'HU' => ['country' => 'Hungary', 'alpha2_code' => 'HU', 'alpha3_code' => 'HUN', 'numeric' => '348', 'sepa' => true,  'format' => 'HUkk bbbs sssx cccc cccc cccc cccx'],
        'IS' => ['country' => 'Iceland', 'alpha2_code' => 'IS', 'alpha3_code' => 'ISL', 'numeric' => '352', 'sepa' => true,  'format' => 'ISkk bbss ttcc cccc iiii iiii ii'],
        'IQ' => ['country' => 'Iraq', 'alpha2_code' => 'IQ', 'alpha3_code' => 'IRQ', 'numeric' => '368', 'sepa' => false, 'format' => '	IQkk bbbb sssc cccc cccc ccc'],
        'IE' => ['country' => 'Ireland', 'alpha2_code' => 'IE', 'alpha3_code' => 'IRL', 'numeric' => '372', 'sepa' => true,  'format' => 'IEkk qqqq bbbb bbcc cccc cc'],
        'IL' => ['country' => 'Israel', 'alpha2_code' => 'IL', 'alpha3_code' => 'ISR', 'numeric' => '376', 'sepa' => false,  'format' => 'ILkk bbbs sscc cccc cccc ccc'],
        'IT' => ['country' => 'Italy', 'alpha2_code' => 'IT', 'alpha3_code' => 'ITA', 'numeric' => '380', 'sepa' => true,  'format' => 'ITkk xbbb bbss sssc cccc cccc ccc'],
        'JO' => ['country' => 'Jordan', 'alpha2_code' => 'JO', 'alpha3_code' => 'JOR', 'numeric' => '400', 'sepa' => false,  'format' => 'JOkk bbbb ssss cccc cccc cccc cccc cc'],
        'KZ' => ['country' => 'Kazakhstan', 'alpha2_code' => 'KZ', 'alpha3_code' => 'KAZ', 'numeric' => '398', 'sepa' => false,  'format' => 'KZkk bbbc cccc cccc cccc'],
        'XK' => ['country' => 'Kosovo',  'alpha2_code' => 'XK', 'alpha3_code' => 'XXK', 'numeric' => '', 'sepa' => false, 'format' => 'XKkk bbbb cccc cccc cccc'],
        'KW' => ['country' => 'Kuwait', 'alpha2_code' => 'KW', 'alpha3_code' => 'KWT', 'numeric' => '414', 'sepa' => false, 'format' => 'KWkk bbbb cccc cccc cccc cccc cccc cc'],
        'LV' => ['country' => 'Latvia', 'alpha2_code' => 'LV', 'alpha3_code' => 'LVA', 'numeric' => '428', 'sepa' => true,  'format' => 'LVkk bbbb cccc cccc cccc c'],
        'LB' => ['country' => 'Lebanon', 'alpha2_code' => 'LB', 'alpha3_code' => 'LBN', 'numeric' => '422', 'sepa' => false,  'format' => 'LBkk bbbb cccc cccc cccc cccc cccc'],
        'LY' => ['country' => 'Libya', 'alpha2_code' => 'LY', 'alpha3_code' => 'LBY', 'numeric' => '424', 'sepa' => false, 'format' => 'LYkk bbbs sscc cccc cccc cccc c'],
        'LI' => ['country' => 'Liechtenstein',  'alpha2_code' => 'LI', 'alpha3_code' => 'LIE', 'numeric' => '438', 'sepa' => true, 'format' => 'LIkk bbbb bccc cccc cccc c'],
        'LT' => ['country' => 'Lithuania', 'alpha2_code' => 'LT', 'alpha3_code' => 'LTU', 'numeric' => '440', 'sepa' => true,  'format' => 'LTkk bbbb bccc cccc cccc'],
        'LU' => ['country' => 'Luxembourg', 'alpha2_code' => 'LU', 'alpha3_code' => 'LUX', 'numeric' => '442', 'sepa' => true,  'format' => 'LUkk bbbc cccc cccc cccc'],
        'MK' => ['country' => 'North Macedonia', 'alpha2_code' => 'MK', 'alpha3_code' => 'MKD', 'numeric' => '807', 'sepa' => false,  'format' => 'MKkk bbbc cccc cccc cxx'],
        'MT' => ['country' => 'Malta', 'alpha2_code' => 'MT', 'alpha3_code' => 'MLT', 'numeric' => '470', 'sepa' => true,  'format' => 'MTkk bbbb ssss sccc cccc cccc cccc ccc'],
        'MR' => ['country' => 'Mauritania', 'alpha2_code' => 'MR', 'alpha3_code' => 'MRT', 'numeric' => '478', 'sepa' => false,  'format' => 'MRkk bbbb bsss sscc cccc cccc cxx'],
        'MU' => ['country' => 'Mauritius', 'alpha2_code' => 'MU', 'alpha3_code' => 'MUS', 'numeric' => '480', 'sepa' => false,  'format' => 'MUkk bbbb bbss cccc cccc cccc 000m mm'],
        'MC' => ['country' => 'Monaco', 'alpha2_code' => 'MC', 'alpha3_code' => 'MCO', 'numeric' => '492', 'sepa' => true,  'format' => 'MCkk bbbb bsss sscc cccc cccc cxx'],
        'MD' => ['country' => 'Moldova', 'alpha2_code' => 'MD', 'alpha3_code' => 'MDA', 'numeric' => '498', 'sepa' => false,  'format' => 'MDkk bbcc cccc cccc cccc cccc'],
        'ME' => ['country' => 'Montenegro', 'alpha2_code' => 'ME', 'alpha3_code' => 'MNE', 'numeric' => '499', 'sepa' => false,  'format' => 'MEkk bbbc cccc cccc cccc xx'],
        'NL' => ['country' => 'Netherlands', 'alpha2_code' => 'NL', 'alpha3_code' => 'NLD', 'numeric' => '528', 'sepa' => true,  'format' => 'NLkk bbbb cccc cccc cc'],
        'NO' => ['country' => 'Norway', 'alpha2_code' => 'NO', 'alpha3_code' => 'NOR', 'numeric' => '', 'sepa' => true, 'format' => 'NOkk bbbb cccc ccx'],
        'PK' => ['country' => 'Pakistan', 'alpha2_code' => 'PK', 'alpha3_code' => 'PAK', 'numeric' => '586', 'sepa' => false,  'format' => 'PKkk bbbb cccc cccc cccc cccc'],
        'PS' => ['country' => 'Palestinian territories', 'alpha2_code' => 'PS', 'alpha3_code' => 'PSE', 'numeric' => '275', 'sepa' => false,  'format' => 'PSkk bbbb cccc cccc cccc cccc cccc c'],
        'PL' => ['country' => 'Poland', 'alpha2_code' => 'PL', 'alpha3_code' => 'POL', 'numeric' => '616', 'sepa' => true,  'format' => 'PLkk bbbs sssx cccc cccc cccc cccc'],
        'PT' => ['country' => 'Portugal', 'alpha2_code' => 'PT', 'alpha3_code' => 'PRT', 'numeric' => '620', 'sepa' => true,  'format' => 'PTkk bbbb ssss cccc cccc cccx x'],
        'QA' => ['country' => 'Quatar', 'alpha2_code' => 'QA', 'alpha3_code' => 'QAT', 'numeric' => '634', 'sepa' => false,  'format' => 'QAkk bbbb cccc cccc cccc cccc cccc c'],
        'RO' => ['country' => 'Romania', 'alpha2_code' => 'RO', 'alpha3_code' => 'ROU', 'numeric' => '642', 'sepa' => true,  'format' => 'ROkk bbbb cccc cccc cccc cccc'],
        'RU' => ['country' => 'Russia', 'alpha2_code' => 'RU', 'alpha3_code' => 'RUS', 'numeric' => '643', 'sepa' => false,  'format' => 'RUkk bbbb bbbb bsss sscc cccc cccc cccc c'],
        'LC' => ['country' => 'Santa Lucia', 'alpha2_code' => 'LC', 'alpha3_code' => 'LCA', 'numeric' => '662', 'sepa' => false,  'format' => 'LCkk bbbb cccc cccc cccc cccc cccc cccc'],
        'SM' => ['country' => 'San Marino', 'alpha2_code' => 'SM', 'alpha3_code' => 'SMR', 'numeric' => '674', 'sepa' => true,  'format' => 'SMkk xbbb bbss sssc cccc cccc ccc'],
        'ST' => ['country' => 'São Tomé and Príncipe', 'alpha2_code' => 'ST', 'alpha3_code' => 'STP', 'numeric' => '678', 'sepa' => false,  'format' => 'STkk bbbb ssss cccc cccc cccc c'],
        'SA' => ['country' => 'Saudi Arabia', 'alpha2_code' => 'SA', 'alpha3_code' => 'SAU', 'numeric' => '682', 'sepa' => false,  'format' => 'SAkk bbcc cccc cccc cccc cccc'],
        'RS' => ['country' => 'Serbia', 'alpha2_code' => 'RS', 'alpha3_code' => 'SRB', 'numeric' => '688', 'sepa' => false,  'format' => 'RSkk bbbc cccc cccc cccc xx'],
        'SC' => ['country' => 'Seychelles', 'alpha2_code' => 'SC', 'alpha3_code' => 'SYC', 'numeric' => '690', 'sepa' => false,  'format' => 'SCkk bbbb bb ss cccc cccc cccc cccc mmm'],
        'SK' => ['country' => 'Slovakia', 'alpha2_code' => 'SK', 'alpha3_code' => 'SVK', 'numeric' => '703', 'sepa' => true, 'format' => 'SKkk bbbb pppp sscc cccc cccc'],
        'SI' => ['country' => 'Slovenia', 'alpha2_code' => 'SI', 'alpha3_code' => 'SVN', 'numeric' => '705', 'sepa' => true,  'format' => 'SIkk bbss sccc cccc cxx'],
        'ES' => ['country' => 'Spain', 'alpha2_code' => 'ES', 'alpha3_code' => 'ESP', 'numeric' => '724', 'sepa' => true,  'format' => 'ESkk bbbb ssss xxcc cccc cccc'],
        'SD' => ['country' => 'Sudan',  'alpha2_code' => 'SD', 'alpha3_code' => 'SDN', 'numeric' => '729', 'sepa' => false, 'format' => 'SDkk bbcc cccc cccc cc'],
        'SE' => ['country' => 'Sweden', 'alpha2_code' => 'SE', 'alpha3_code' => 'SWE', 'numeric' => '752', 'sepa' => true,  'format' => 'SEkk bbbc cccc cccc cccc cccx'],
        'CH' => ['country' => 'Switzerland', 'alpha2_code' => 'CH', 'alpha3_code' => 'CHE', 'numeric' => '756', 'sepa' => true,  'format' => 'CHkk bbbb bccc cccc cccc c'],
        'TN' => ['country' => 'Tunisia', 'alpha2_code' => 'TN', 'alpha3_code' => 'TUN', 'numeric' => '788', 'sepa' => false,  'format' => 'TNkk bbss sccc cccc cccc ccxx'],
        'TR' => ['country' => 'Turkey', 'alpha2_code' => 'TR', 'alpha3_code' => 'TUR', 'numeric' => '792', 'sepa' => false,  'format' => 'TRkk bbbb b0cc cccc cccc cccc cc'],
        'UA' => ['country' => 'Ukraine', 'alpha2_code' => 'UA', 'alpha3_code' => 'UKR', 'numeric' => '804', 'sepa' => false,  'format' => 'UAkk bbbb bbcc cccc cccc cccc cccc c'],
        'AE' => ['country' => 'United Arab Emirates', 'alpha2_code' => 'AE', 'alpha3_code' => 'ARE', 'numeric' => '784', 'sepa' => false,  'format' => 'AEkk bbbc cccc cccc cccc ccc'],
        'GB' => ['country' => 'United Kingdom', 'alpha2_code' => 'GB', 'alpha3_code' => 'GBR', 'numeric' => '826', 'sepa' => true,  'format' => 'GBkk bbbb ssss sscc cccc cc'],
        'VA' => ['country' => 'Vatikan City',  'alpha2_code' => 'VA', 'alpha3_code' => 'VAT', 'numeric' => '', 'sepa' => false, 'format' => 'VAkk bbbc cccc cccc cccc cc'],
        'VG' => ['country' => 'Virgin Islands, British', 'alpha2_code' => 'VG', 'alpha3_code' => 'VGB', 'numeric' => '092', 'sepa' => false,  'format' => 'VGkk bbbb cccc cccc cccc cccc'],
    ];


    /**
     * SWIFT for countries, add more as needed
     */
    private const SWIFT = [
        'SK' => [
            '0200' => 'SUBASKBX','0900' => 'GIBASKBX','0720' => 'NBSBSKBX',
            '1100' => 'TATRSKBX','1111' => 'UNCRSKBX','3000' => 'SLZBSKBA',
            '3100' => 'LUBASKBX','5200' => 'OTPVSKBX','5600' => 'KOMASK2X',
            '5900' => 'PRVASKBA','6500' => 'POBNSKBA','7300' => 'INGBSKBX',
            '7500' => 'CEKOSKBX','7930' => 'WUSTSKBA','8050' => 'COBASKBX',
            '8100' => 'KOMBSKBA','8120' => 'BSLOSK22','8130' => 'CITISKBA',
            '8150' => 'ABNASKBX','8170' => 'KBSPSKBX','8160' => 'EXSKSKBX',
            '8180' => 'SPSRSKBA','8320' => 'JTBPSKBA','8330' => 'FIOZSKBA',
            '8360' => 'BREXSKBX','8370' => 'OBKLSKBA','8410' => 'RIDBSKBX',
            '8420' => 'BFKKSKBB','8430' => 'KODBSKBX','9951' => 'XBRASKB1',
            '9952' => 'TPAYSKBX'
        ],
    ];

    /**
     * @var string
     */
    private string $countryCode;


    /**
     * @param string $value
     * @param string $countryCode
     */
    private function __construct(string $value, string $countryCode)
    {
        $this->value = $value;
        $this->countryCode = $countryCode;
    }

    /**
     * @param string $value
     * @return static
     */
    public static function create(string $value): self
    {
        // converts to upper case and remove spaces
        $iban = strtoupper(str_replace(' ','',$value));
        $countryCode = substr($iban,0,2);

        if (! isset(self::IBAN_COUNTRY_MAP[$countryCode])) {
            throw new InvalidArgumentException("Invalid country code $countryCode for IBAN $value");
        }

        $format = self::removeSpaces(self::IBAN_COUNTRY_MAP[$countryCode]['format']);
        if(strlen($format) !== strlen($iban)) {
            throw new InvalidArgumentException("Invalid IBAN length for country $countryCode. Expected " .  strlen($format) . " characters");
        }

        if (!self::isValidCheckSum($iban)) {
            throw new InvalidArgumentException("Invalid check sum for IBAN $value");
        }

        return new self($iban, $countryCode);
    }

    /**
     * @param string $iban
     * @return bool
     *
     * IBAN check sum validation
     *
     * 1. The first four characters are moved to the end of the number
     * 2. The letters are translated into numbers, according to the char translation table
     * 3. Number is divided by 97
     * 4. If the modulo (remainder after the integer division) is 1, then the initial account number is a
     *    correct ΙΒΑΝ format; else this is not an IBAN account number
     */
    private static function isValidCheckSum(string $iban): bool
    {
        // remove country code and check digits
        $countryCodeWithCheckDigits = substr($iban, 0, 4);

        // put country code with check digit at the end of IBAN
        $reverted = substr($iban, 4) . $countryCodeWithCheckDigits;

        // map to char translation table
        $reverted = strtr($reverted, self::CHARS);

        // module 97
        return bcmod($reverted, self::MODULO) == 1;
    }

    /**
     * @return string
     */
    public function getCountry(): string
    {
        return self::IBAN_COUNTRY_MAP[$this->countryCode]['country'];
    }

    /**
     * @return string
     */
    public function getAlpha2CountryCode(): string
    {
        return self::IBAN_COUNTRY_MAP[$this->countryCode]['alpha2_code'];
    }

    /**
     * @return string
     */
    public function getAlpha3CountryCode(): string
    {
        return self::IBAN_COUNTRY_MAP[$this->countryCode]['alpha3_code'];
    }

    /**
     * @return string
     */
    public function getNumericCountryCode(): string
    {
        return self::IBAN_COUNTRY_MAP[$this->countryCode]['numeric'];
    }

    /**
     * @return string
     */
    public function isSepaEnabled(): string
    {
        return self::IBAN_COUNTRY_MAP[$this->countryCode]['sepa'];
    }

    /**
     * @return string
     */
    public function getSwift(): string
    {
        $bankCode = $this->getNationalBankCode();
        if (!isset(self::SWIFT[$this->countryCode][$bankCode])) {
            return "";
        }

        return self::SWIFT[$this->countryCode][$bankCode];
    }

    /**
     * @return string
     */
    public function getAccountNumber(): string
    {
        return $this->getValueFromIban('c');
    }

    /**
     * @return string
     */
    public function getNationalBankCode(): string
    {
        return $this->getValueFromIban('b');
    }

    /**
     * @return string
     */
    public function getAccountNumberPrefix(): string
    {
        return $this->getValueFromIban('p');
    }

    /**
     * @param string $needle
     * @return string
     *
     * Get specific value from IBAN based on the IBAN format by needle key
     */
    private function getValueFromIban(string $needle): string
    {
        $format = $this->getIbanFormat();
        $firstOccurrence = strpos($format, $needle);
        $lastOccurrence = strripos($format, $needle) +1;

        if (!$firstOccurrence) {
            return "";
        }

        return substr($this->value, $firstOccurrence, $lastOccurrence - $firstOccurrence);
    }

    /**
     * @return string
     */
    private function getIbanFormat(): string
    {
        return self::removeSpaces(self::IBAN_COUNTRY_MAP[$this->countryCode]['format']);
    }

    /**
     * @param string $string
     * @return string
     */
    private static function removeSpaces(string $string): string
    {
        return str_replace(' ','', $string);
    }
}