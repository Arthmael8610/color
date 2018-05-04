<?php

namespace App\Http\Controllers;

use App\Company;
use App\Events\CompUser;
use App\Events\NewUser;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

use App\Http\Controllers\AdminController as Admin;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class CompanyController extends Controller
{

    public $data;
    public $user;
    public $comp;

    public function __construct()
    {
        $admin = new Admin;
        $this->user = new User;
        $this->comp = new Company;

        $activeMembers = $admin->getTotalMemberInfo();
        $expiring = $admin->getExpiringAccounts();
        $newMembers = $admin->getMonthlyNewMembers();

        $this->data = [
            'activeMembers' => $activeMembers,
            'totalMembers' => $this->user->count(),
            'expiringMembers' => $expiring,
            'newMembers' => $newMembers,
        ];
        $this->data['states'] = array(
            'AL' => 'Alabama',
            'AK' => 'Alaska',
            'AZ' => 'Arizona',
            'AR' => 'Arkansas',
            'CA' => 'California',
            'CO' => 'Colorado',
            'CT' => 'Connecticut',
            'DE' => 'Delaware',
            'DC' => 'District Of Columbia',
            'FL' => 'Florida',
            'GA' => 'Georgia',
            'HI' => 'Hawaii',
            'ID' => 'Idaho',
            'IL' => 'Illinois',
            'IN' => 'Indiana',
            'IA' => 'Iowa',
            'KS' => 'Kansas',
            'KY' => 'Kentucky',
            'LA' => 'Louisiana',
            'ME' => 'Maine',
            'MD' => 'Maryland',
            'MA' => 'Massachusetts',
            'MI' => 'Michigan',
            'MN' => 'Minnesota',
            'MS' => 'Mississippi',
            'MO' => 'Missouri',
            'MT' => 'Montana',
            'NE' => 'Nebraska',
            'NV' => 'Nevada',
            'NH' => 'New Hampshire',
            'NJ' => 'New Jersey',
            'NM' => 'New Mexico',
            'NY' => 'New York',
            'NC' => 'North Carolina',
            'ND' => 'North Dakota',
            'OH' => 'Ohio',
            'OK' => 'Oklahoma',
            'OR' => 'Oregon',
            'PA' => 'Pennsylvania',
            'RI' => 'Rhode Island',
            'SC' => 'South Carolina',
            'SD' => 'South Dakota',
            'TN' => 'Tennessee',
            'TX' => 'Texas',
            'UT' => 'Utah',
            'VT' => 'Vermont',
            'VA' => 'Virginia',
            'WA' => 'Washington',
            'WV' => 'West Virginia',
            'WI' => 'Wisconsin',
            'WY' => 'Wyoming',
            'OS' => 'Outside United States',
            '' => '',
            'AB' => 'Alberta',
            'BC' => 'Brittish Columbia',
            'MB' => 'Manitoba',
            'NB' => 'New Brunswick',
            'NL' => 'Newfoundland and Labrador',
            'NT' => 'Northwest Territories',
            'NS' => 'Nova Scotia',
            'NU' => 'Nunavut',
            'ON' => 'Ontario',
            'PE' => 'Prince Edward Island',
            'QC' => 'Quebec',
            'SK' => 'Saskatchewan',
            'YT' => 'Yukon',
            'WAL' => 'Wales',
            'IE' => 'Ireland',
            'SCT'=> 'Scotland',
            'ENG' => 'England',
        );
        $this->data['country'] = array(
            'US' => 'United States',
            'AF' => 'Afghanistan',
            'AX' => 'Aland Islands',
            'AL' => 'Albania',
            'DZ' => 'Algeria',
            'AS' => 'American Samoa',
            'AD' => 'Andorra',
            'AO' => 'Angola',
            'AI' => 'Anguilla',
            'AQ' => 'Antarctica',
            'AG' => 'Antigua And Barbuda',
            'AR' => 'Argentina',
            'AM' => 'Armenia',
            'AW' => 'Aruba',
            'AU' => 'Australia',
            'AT' => 'Austria',
            'AZ' => 'Azerbaijan',
            'BS' => 'Bahamas',
            'BH' => 'Bahrain',
            'BD' => 'Bangladesh',
            'BB' => 'Barbados',
            'BY' => 'Belarus',
            'BE' => 'Belgium',
            'BZ' => 'Belize',
            'BJ' => 'Benin',
            'BM' => 'Bermuda',
            'BT' => 'Bhutan',
            'BO' => 'Bolivia',
            'BA' => 'Bosnia And Herzegovina',
            'BW' => 'Botswana',
            'BV' => 'Bouvet Island',
            'BR' => 'Brazil',
            'IO' => 'British Indian Ocean Territory',
            'BN' => 'Brunei Darussalam',
            'BG' => 'Bulgaria',
            'BF' => 'Burkina Faso',
            'BI' => 'Burundi',
            'KH' => 'Cambodia',
            'CM' => 'Cameroon',
            'CA' => 'Canada',
            'CV' => 'Cape Verde',
            'KY' => 'Cayman Islands',
            'CF' => 'Central African Republic',
            'TD' => 'Chad',
            'CL' => 'Chile',
            'CN' => 'China',
            'CX' => 'Christmas Island',
            'CC' => 'Cocos (Keeling) Islands',
            'CO' => 'Colombia',
            'KM' => 'Comoros',
            'CG' => 'Congo',
            'CD' => 'Congo, Democratic Republic',
            'CK' => 'Cook Islands',
            'CR' => 'Costa Rica',
            'CI' => 'Cote D\'Ivoire',
            'HR' => 'Croatia',
            'CU' => 'Cuba',
            'CY' => 'Cyprus',
            'CZ' => 'Czech Republic',
            'DK' => 'Denmark',
            'DJ' => 'Djibouti',
            'DM' => 'Dominica',
            'DO' => 'Dominican Republic',
            'EC' => 'Ecuador',
            'EG' => 'Egypt',
            'SV' => 'El Salvador',
            'GQ' => 'Equatorial Guinea',
            'ER' => 'Eritrea',
            'EE' => 'Estonia',
            'ET' => 'Ethiopia',
            'FK' => 'Falkland Islands (Malvinas)',
            'FO' => 'Faroe Islands',
            'FJ' => 'Fiji',
            'FI' => 'Finland',
            'FR' => 'France',
            'GF' => 'French Guiana',
            'PF' => 'French Polynesia',
            'TF' => 'French Southern Territories',
            'GA' => 'Gabon',
            'GM' => 'Gambia',
            'GE' => 'Georgia',
            'DE' => 'Germany',
            'GH' => 'Ghana',
            'GI' => 'Gibraltar',
            'GR' => 'Greece',
            'GL' => 'Greenland',
            'GD' => 'Grenada',
            'GP' => 'Guadeloupe',
            'GU' => 'Guam',
            'GT' => 'Guatemala',
            'GG' => 'Guernsey',
            'GN' => 'Guinea',
            'GW' => 'Guinea-Bissau',
            'GY' => 'Guyana',
            'HT' => 'Haiti',
            'HM' => 'Heard Island & Mcdonald Islands',
            'VA' => 'Holy See (Vatican City State)',
            'HN' => 'Honduras',
            'HK' => 'Hong Kong',
            'HU' => 'Hungary',
            'IS' => 'Iceland',
            'IN' => 'India',
            'ID' => 'Indonesia',
            'IR' => 'Iran, Islamic Republic Of',
            'IQ' => 'Iraq',
            'IM' => 'Isle Of Man',
            'IL' => 'Israel',
            'IT' => 'Italy',
            'JM' => 'Jamaica',
            'JP' => 'Japan',
            'JE' => 'Jersey',
            'JO' => 'Jordan',
            'KZ' => 'Kazakhstan',
            'KE' => 'Kenya',
            'KI' => 'Kiribati',
            'KR' => 'Korea',
            'KW' => 'Kuwait',
            'KG' => 'Kyrgyzstan',
            'LA' => 'Lao People\'s Democratic Republic',
            'LV' => 'Latvia',
            'LB' => 'Lebanon',
            'LS' => 'Lesotho',
            'LR' => 'Liberia',
            'LY' => 'Libyan Arab Jamahiriya',
            'LI' => 'Liechtenstein',
            'LT' => 'Lithuania',
            'LU' => 'Luxembourg',
            'MO' => 'Macao',
            'MK' => 'Macedonia',
            'MG' => 'Madagascar',
            'MW' => 'Malawi',
            'MY' => 'Malaysia',
            'MV' => 'Maldives',
            'ML' => 'Mali',
            'MT' => 'Malta',
            'MH' => 'Marshall Islands',
            'MQ' => 'Martinique',
            'MR' => 'Mauritania',
            'MU' => 'Mauritius',
            'YT' => 'Mayotte',
            'MX' => 'Mexico',
            'FM' => 'Micronesia, Federated States Of',
            'MD' => 'Moldova',
            'MC' => 'Monaco',
            'MN' => 'Mongolia',
            'ME' => 'Montenegro',
            'MS' => 'Montserrat',
            'MA' => 'Morocco',
            'MZ' => 'Mozambique',
            'MM' => 'Myanmar',
            'NA' => 'Namibia',
            'NR' => 'Nauru',
            'NP' => 'Nepal',
            'NL' => 'Netherlands',
            'AN' => 'Netherlands Antilles',
            'NC' => 'New Caledonia',
            'NZ' => 'New Zealand',
            'NI' => 'Nicaragua',
            'NE' => 'Niger',
            'NG' => 'Nigeria',
            'NU' => 'Niue',
            'NF' => 'Norfolk Island',
            'MP' => 'Northern Mariana Islands',
            'NO' => 'Norway',
            'OM' => 'Oman',
            'PK' => 'Pakistan',
            'PW' => 'Palau',
            'PS' => 'Palestinian Territory, Occupied',
            'PA' => 'Panama',
            'PG' => 'Papua New Guinea',
            'PY' => 'Paraguay',
            'PE' => 'Peru',
            'PH' => 'Philippines',
            'PN' => 'Pitcairn',
            'PL' => 'Poland',
            'PT' => 'Portugal',
            'PR' => 'Puerto Rico',
            'QA' => 'Qatar',
            'RE' => 'Reunion',
            'RO' => 'Romania',
            'RU' => 'Russian Federation',
            'RW' => 'Rwanda',
            'BL' => 'Saint Barthelemy',
            'SH' => 'Saint Helena',
            'KN' => 'Saint Kitts And Nevis',
            'LC' => 'Saint Lucia',
            'MF' => 'Saint Martin',
            'PM' => 'Saint Pierre And Miquelon',
            'VC' => 'Saint Vincent And Grenadines',
            'WS' => 'Samoa',
            'SM' => 'San Marino',
            'ST' => 'Sao Tome And Principe',
            'SA' => 'Saudi Arabia',
            'SN' => 'Senegal',
            'RS' => 'Serbia',
            'SC' => 'Seychelles',
            'SL' => 'Sierra Leone',
            'SG' => 'Singapore',
            'SK' => 'Slovakia',
            'SI' => 'Slovenia',
            'SB' => 'Solomon Islands',
            'SO' => 'Somalia',
            'ZA' => 'South Africa',
            'GS' => 'South Georgia And Sandwich Isl.',
            'ES' => 'Spain',
            'LK' => 'Sri Lanka',
            'SD' => 'Sudan',
            'SR' => 'Suriname',
            'SJ' => 'Svalbard And Jan Mayen',
            'SZ' => 'Swaziland',
            'SE' => 'Sweden',
            'CH' => 'Switzerland',
            'SY' => 'Syrian Arab Republic',
            'TW' => 'Taiwan',
            'TJ' => 'Tajikistan',
            'TZ' => 'Tanzania',
            'TH' => 'Thailand',
            'TL' => 'Timor-Leste',
            'TG' => 'Togo',
            'TK' => 'Tokelau',
            'TO' => 'Tonga',
            'TT' => 'Trinidad And Tobago',
            'TN' => 'Tunisia',
            'TR' => 'Turkey',
            'TM' => 'Turkmenistan',
            'TC' => 'Turks And Caicos Islands',
            'TV' => 'Tuvalu',
            'UG' => 'Uganda',
            'UA' => 'Ukraine',
            'AE' => 'United Arab Emirates',
            'GB' => 'United Kingdom',
            'US' => 'United States',
            'UM' => 'United States Outlying Islands',
            'UY' => 'Uruguay',
            'UZ' => 'Uzbekistan',
            'VU' => 'Vanuatu',
            'VE' => 'Venezuela',
            'VN' => 'Viet Nam',
            'VG' => 'Virgin Islands, British',
            'VI' => 'Virgin Islands, U.S.',
            'WF' => 'Wallis And Futuna',

            'EH' => 'Western Sahara',
            'YE' => 'Yemen',
            'ZM' => 'Zambia',
            'ZW' => 'Zimbabwe',
        );
        $this->data['industry'] = array(
            'Academia' => 'Academia',
            'Advertising/Design' => 'Advertising/Design',
            'Aerosol Color Sprays' => 'Aerosol Color Sprays',
            'Aluminum Printing' => 'Aluminum Printing',
            'Architectural Coating' => 'Architectural Coating',
            'Architectural Colour & Material' => 'Architectural Colour & Material',
            'Architecture' => 'Architecture',
            'Architecture & Design' => 'Architecture & Design',
            'Automotive' => 'Automotive',
            'Automotive Aftermarket' => 'Automotive Aftermarket',
            'Automotive Chemicals' => 'Automotive Chemicals',
            'Biking Manufacturer' => 'Biking Manufacturer',
            'Bldg Materials' => 'Bldg Materials',
            'Bldg Materials/Mfgr Laminate' => 'Bldg Materials/Mfgr Laminate',
            'Branding' => 'Branding',
            'Building Products' => 'Building Products',
            'Cabinet Manufacturer' => 'Cabinet Manufacturer',
            'Cabinetry Colors' => 'Cabinetry Colors',
            'Cabinetry/Furniture' => 'Cabinetry/Furniture',
            'Carpet/Rug' => 'Carpet/Rug',
            'Ceramic Tile' => 'Ceramic Tile',
            'Chemical Industry' => 'Chemical Industry',
            'Chemistry' => 'Chemistry',
            'Coatings' => 'Coatings',
            'Color' => 'Color',
            'Color & Design Consulting' => 'Color & Design Consulting',
            'Color Design of Facades' => 'Color Design of Facades',
            'Color Design/Surface Design' => 'Color Design/Surface Design',
            'Color Samples' => 'Color Samples',
            'Color Science/ Interior Design' => 'Color Science/ Interior Design',
            'Color Solutions' => 'Color Solutions',
            'Color Tools' => 'Color Tools',
            'Colorant Dispersions' => 'Colorant Dispersions',
            'Colorants' => 'Colorants',
            'Colour' => 'Colour',
            'Commercial Carpet' => 'Commercial Carpet',
            'Commercial Carpet Fiber' => 'Commercial Carpet Fiber',
            'Commercial Construction/Exterior' => 'Commercial Construction/Exterior',
            'Commercial Flooring' => 'Commercial Flooring',
            'Commercial Interiors' => 'Commercial Interiors',
            'Commercial Office Furniture' => 'Commercial Office Furniture',
            'Commerical LVT Flooring' => 'Commerical LVT Flooring',
            'Consultancy' => 'Consultancy',
            'Consumer electronics' => 'Consumer electronics',
            'Consumer Goods' => 'Consumer Goods',
            'Consumer Products' => 'Consumer Products',
            'Contract Carpet' => 'Contract Carpet',
            'Contract Furniture' => 'Contract Furniture',
            'Contract Wallcovering' => 'Contract Wallcovering',
            'Conventions' => 'Conventions',
            'Corporate, Healthcare, Education' => 'Corporate, Healthcare, Education',
            'Cosmetics' => 'Cosmetics',
            'Craft - Handknitting Yarn' => 'Craft - Handknitting Yarn',
            'Data Collection & Mgmt' => 'Data Collection & Mgmt',
            'Decorating' => 'Decorating',
            'Decorative Coatings' => 'Decorative Coatings',
            'Decorative Laminates' => 'Decorative Laminates',
            'Decorative Printer Paper' => 'Decorative Printer Paper',
            'Decorative Surfaces' => 'Decorative Surfaces',
            'Design' => 'Design',
            'Design & Architectural Software' => 'Design & Architectural Software',
            'Design & Innovation' => 'Design & Innovation',
            'Design Marketing' => 'Design Marketing',
            'Education' => 'Education',
            'Effect Pigments' => 'Effect Pigments',
            'Entertainment' => 'Entertainment',
            'Event Design/Hospitality Design' => 'Event Design/Hospitality Design',
            'Fabric Converter' => 'Fabric Converter',
            'Fashion' => 'Fashion',
            'Floor covering' => 'Floor covering',
            'Flooring' => 'Flooring',
            'Flooring distributor' => 'Flooring distributor',
            'Flooring/ Ceramic Accessories' => 'Flooring/ Ceramic Accessories',
            'Food Color/Decorations' => 'Food Color/Decorations',
            'Furniture' => 'Furniture',
            'Furniture Manufacturing' => 'Furniture Manufacturing',
            'Gardening Consumer Goods' => 'Gardening Consumer Goods',
            'Global Design Consultant' => 'Global Design Consultant',
            'Graphic Design' => 'Graphic Design',
            'Graphic Design/Photography/Color Gem Stones' => 'Graphic Design/Photography/Color Gem Stones',
            'Graphic/Industrial Design' => 'Graphic/Industrial Design',
            'Hard Goods/Home Décor' => 'Hard Goods/Home Décor',
            'Hard Surfacing' => 'Hard Surfacing',
            'Hardware' => 'Hardware',
            'High-Performance blenders' => 'High-Performance blenders',
            'Home Building' => 'Home Building',
            'Home décor' => 'Home décor',
            'Home Furnishings' => 'Home Furnishings',
            'Home Interiors' => 'Home Interiors',
            'Home Textiles' => 'Home Textiles',
            'Home/Architectural Coatings' => 'Home/Architectural Coatings',
            'Home/Cabinetry' => 'Home/Cabinetry',
            'Housewares' => 'Housewares',
            'Industrial Coatings' => 'Industrial Coatings',
            'Interior Decorative Surfaces' => 'Interior Decorative Surfaces',
            'Interior Design' => 'Interior Design',
            'Interior Surfacing Products' => 'Interior Surfacing Products',
            'Kitchen Cabinets' => 'Kitchen Cabinets',
            'Laminates' => 'Laminates',
            'Leather' => 'Leather',
            'Lifestyle' => 'Lifestyle',
            'Liquid & Powder Coatings' => 'Liquid & Powder Coatings',
            'Manufacturer of Plastic Housewares' => 'Manufacturer of Plastic Housewares',
            'Manufacturers, Laminates' => 'Manufacturers, Laminates',
            'Manufacturing' => 'Manufacturing',
            'Manufacturing Flooring' => 'Manufacturing Flooring',
            'Manufacturing/Distribution' => 'Manufacturing/Distribution',
            'Marine' => 'Marine',
            'Marketing' => 'Marketing',
            'Marketing & Design' => 'Marketing & Design',
            'Masterbatch Producer' => 'Masterbatch Producer',
            'Material Supplier' => 'Material Supplier',
            'Materials Manufacturer' => 'Materials Manufacturer',
            'Mattress Ticking' => 'Mattress Ticking',
            'New Home Construction/ Home Interiors' => 'New Home Construction/ Home Interiors',
            'New Residential Home Construction' => 'New Residential Home Construction',
            'OEM Paint Supplier' => 'OEM Paint Supplier',
            'Office Furniture' => 'Office Furniture',
            'Outdoor, Home' => 'Outdoor, Home',
            'Paint' => 'Paint',
            'Paint & Coatings' => 'Paint & Coatings',
            'Paint & Furniture' => 'Paint & Furniture',
            'Paint & Specialty Coatings' => 'Paint & Specialty Coatings',
            'Paint Manufacturers' => 'Paint Manufacturers',
            'Paint/Stains/Design & Colour' => 'Paint/Stains/Design & Colour',
            'Paint/Wallcoverings' => 'Paint/Wallcoverings',
            'Paper printing' => 'Paper printing',
            'Performance Materials/Pigments' => 'Performance Materials/Pigments',
            'Personal Electronics' => 'Personal Electronics',
            'Plastic Compounding' => 'Plastic Compounding',
            'Plastics' => 'Plastics',
            'Plastics & Coatings' => 'Plastics & Coatings',
            'Plastics & Masterbatch' => 'Plastics & Masterbatch',
            'Plastics Color' => 'Plastics Color',
            'Plastics Compounding' => 'Plastics Compounding',
            'Plastics Packaging' => 'Plastics Packaging',
            'Powder Coatings' => 'Powder Coatings',
            'Prine' => 'Prine',
            'Printers of Décor Paper' => 'Printers of Décor Paper',
            'Product Development Consulting' => 'Product Development Consulting',
            'Promotional' => 'Promotional',
            'Quartz & Marble Manufacturer' => 'Quartz & Marble Manufacturer',
            'Residential  ' => 'Residential  ',
            'Residential & Commercial Interiors' => 'Residential & Commercial Interiors',
            'Residential Cabinetry' => 'Residential Cabinetry',
            'Residential Carpet' => 'Residential Carpet',
            'Residential Upholstery' => 'Residential Upholstery',
            'Residential/Commercial' => 'Residential/Commercial',
            'Retail (Home)' => 'Retail (Home)',
            'Retail Paint & Interior Design' => 'Retail Paint & Interior Design',
            'Seasonal décor' => 'Seasonal décor',
            'Services' => 'Services',
            'Small Domestic Appliances' => 'Small Domestic Appliances',
            'Soft Floor Covering' => 'Soft Floor Covering',
            'Specialty Coatings' => 'Specialty Coatings',
            'Sport' => 'Sport',
            'Surfacing' => 'Surfacing',
            'Tech' => 'Tech',
            'Textile Flooring' => 'Textile Flooring',
            'Textiles' => 'Textiles',
            'Textiles Furniture' => 'Textiles Furniture',
            'Thermoplastics' => 'Thermoplastics',
            'Tile & Ceramics' => 'Tile & Ceramics',
            'Tile Manufacturing' => 'Tile Manufacturing',
            'Trend Forecasting' => 'Trend Forecasting',
            'Trend Research   ' => 'Trend Research   ',
            'Trend Research & Innovation' => 'Trend Research & Innovation',
            'Vinyl - Window Coverings' => 'Vinyl - Window Coverings',
            'Wall Décor' => 'Wall Décor',
            'White Goods' => 'White Goods',
            'Window coverings' => 'Window coverings',
            'Wood based panels' => 'Wood based panels',
            'Wood Products' => 'Wood Products',
            'Woven Upholstery' => 'Woven Upholstery'
        );
        $this->data['position'] = array(
            'Account Executive' => 'Account Executive',
            'Account Manager' => 'Account Manager',
            'Advanced Materials Manager' => 'Advanced Materials Manager',
            'Advertising Coordinator' => 'Advertising Coordinator',
            'Advertising Manager' => 'Advertising Manager',
            'Architect' => 'Architect',
            'Architectural Consultant' => 'Architectural Consultant',
            'Architectural Interiors Rep.' => 'Architectural Interiors Rep.',
            'Art Director' => 'Art Director',
            'Artist' => 'Artist',
            'Assoc Category Manager' => 'Assoc Category Manager',
            'Associate Art Director' => 'Associate Art Director',
            'Associate Designer' => 'Associate Designer',
            'Associate Production Designer' => 'Associate Production Designer',
            'Associate Professor' => 'Associate Professor',
            'Brand Coordinator' => 'Brand Coordinator',
            'Brand Manager' => 'Brand Manager',
            'Brand Marketing Director' => 'Brand Marketing Director',
            'Category Director' => 'Category Director',
            'Category Manager, Product Development' => 'Category Manager, Product Development',
            'CEO' => 'CEO',
            'Chair of the Graduate Studies Dept' => 'Chair of the Graduate Studies Dept',
            'Chief Creative Officer' => 'Chief Creative Officer',
            'Chief Executive Officer' => 'Chief Executive Officer',
            'Chief Marketing Officer' => 'Chief Marketing Officer',
            'CMF Design Manager' => 'CMF Design Manager',
            'CMF Designer' => 'CMF Designer',
            'CMF Global Color Lead' => 'CMF Global Color Lead',
            'CMF Lead' => 'CMF Lead',
            'Color & Design Consultant' => 'Color & Design Consultant',
            'Color & Materials Designer' => 'Color & Materials Designer',
            'Color & Materials Finish Designer' => 'Color & Materials Finish Designer',
            'Color & Materials Manager' => 'Color & Materials Manager',
            'Color & Trend Consultant' => 'Color & Trend Consultant',
            'Color Consultant' => 'Color Consultant',
            'Color Design Studio Manager' => 'Color Design Studio Manager',
            'Color Insight Manager' => 'Color Insight Manager',
            'Color Lab Manager' => 'Color Lab Manager',
            'Color Marketing & Decor Prod Mgr' => 'Color Marketing & Decor Prod Mgr',
            'Color Marketing & Design Specialist' => 'Color Marketing & Design Specialist',
            'Color Marketing Manager' => 'Color Marketing Manager',
            'Color Marketing Project Manager' => 'Color Marketing Project Manager',
            'Color Material Finish Administrator' => 'Color Material Finish Administrator',
            'Color Planner' => 'Color Planner',
            'Color Science Manager' => 'Color Science Manager',
            'Color Specialist' => 'Color Specialist',
            'Color Strategist' => 'Color Strategist',
            'Color Stylist' => 'Color Stylist',
            'Coloristik / Coloristic' => 'Coloristik / Coloristic',
            'Colour & Design Manager' => 'Colour & Design Manager',
            'Colour & Design Specialist' => 'Colour & Design Specialist',
            'Colour Planning & Communications Manager' => 'Colour Planning & Communications Manager',
            'Comercial Director, China' => 'Comercial Director, China',
            'Concept Designer' => 'Concept Designer',
            'Consultant' => 'Consultant',
            'Corporate Color Coordinator' => 'Corporate Color Coordinator',
            'Corporate Design Management' => 'Corporate Design Management',
            'Corporate Interior Designer' => 'Corporate Interior Designer',
            'Creative Director' => 'Creative Director',
            'Creative Manager' => 'Creative Manager',
            'Design & Developent Engineer' => 'Design & Developent Engineer',
            'Design Associate' => 'Design Associate',
            'Design Consultant' => 'Design Consultant',
            'Design Coordinator' => 'Design Coordinator',
            'Design Development Manager' => 'Design Development Manager',
            'Design Director' => 'Design Director',
            'Design Manager' => 'Design Manager',
            'Design Manager - Creative Services' => 'Design Manager - Creative Services',
            'Designer' => 'Designer',
            'Diector of Market Development' => 'Diector of Market Development',
            'Digital Marketing & Creative Director' => 'Digital Marketing & Creative Director',
            'Digital Marketing & Design Manager' => 'Digital Marketing & Design Manager',
            'Director' => 'Director',
            'Director - Architect & Design Channel' => 'Director - Architect & Design Channel',
            'Director BU Coatings & Plastics' => 'Director BU Coatings & Plastics',
            'Director of Business Development & Marketing' => 'Director of Business Development & Marketing',
            'Director of Color Marketing' => 'Director of Color Marketing',
            'Director of Contract Prints' => 'Director of Contract Prints',
            'Director of Creative' => 'Director of Creative',
            'Director of Design' => 'Director of Design',
            'Director of Design & Development' => 'Director of Design & Development',
            'Director of Education' => 'Director of Education',
            'Director of Global Marketing' => 'Director of Global Marketing',
            'Director of Global Product Design' => 'Director of Global Product Design',
            'Director of Manufacturing' => 'Director of Manufacturing',
            'Director of Marketing' => 'Director of Marketing',
            'Director of Marketing Services' => 'Director of Marketing Services',
            'Director of Product Design ' => 'Director of Product Design ',
            'Director of Product Dev & Design' => 'Director of Product Dev & Design',
            'Director of Product Management' => 'Director of Product Management',
            'Director of Sales & Marketing' => 'Director of Sales & Marketing',
            'Director Residential Styling' => 'Director Residential Styling',
            'Director Special Products' => 'Director Special Products',
            'Director,  Designer Relations & Education' => 'Director,  Designer Relations & Education',
            'Director, Interactive Stratety & Design' => 'Director, Interactive Stratety & Design',
            'Director, New Product Development' => 'Director, New Product Development',
            'EVP Design' => 'EVP Design',
            'Executive Communications Manager' => 'Executive Communications Manager',
            'Executive Vice President' => 'Executive Vice President',
            'Founder & Chief Colour Strategist' => 'Founder & Chief Colour Strategist',
            'Freelance Designer' => 'Freelance Designer',
            'Global Marketing' => 'Global Marketing',
            'Graphic Color Specialist' => 'Graphic Color Specialist',
            'Graphic Design   ' => 'Graphic Design   ',
            'Graphic Designer' => 'Graphic Designer',
            'Group VP - Design & Innovation' => 'Group VP - Design & Innovation',
            'Head of Colorworks & Color Communication' => 'Head of Colorworks & Color Communication',
            'Head of Corporate Design Management' => 'Head of Corporate Design Management',
            'Head of Design North America/Coatings Color & Design' => 'Head of Design North America/Coatings Color & Design',
            'Head of Market Development' => 'Head of Market Development',
            'Healthcare Market Manager' => 'Healthcare Market Manager',
            'Independent Color Consultant' => 'Independent Color Consultant',
            'Industrial Design Manager' => 'Industrial Design Manager',
            'In-house Designer' => 'In-house Designer',
            'Integrated Marketing Manager' => 'Integrated Marketing Manager',
            'Interior Designer' => 'Interior Designer',
            'Interior Designer/Kitchen & Bath' => 'Interior Designer/Kitchen & Bath',
            'International Product Executive' => 'International Product Executive',
            'International Sales Manager' => 'International Sales Manager',
            'Kitchen Design Specialist' => 'Kitchen Design Specialist',
            'Lead CMF Designer' => 'Lead CMF Designer',
            'Lifestyle Manager' => 'Lifestyle Manager',
            'Manager of Color Marketing' => 'Manager of Color Marketing',
            'Manager of Design' => 'Manager of Design',
            'Managing Director' => 'Managing Director',
            'Market Development Specialist' => 'Market Development Specialist',
            'Market Research Manager' => 'Market Research Manager',
            'Marketing' => 'Marketing',
            'Marketing & Sales Coordinator' => 'Marketing & Sales Coordinator',
            'Marketing & Sales Director' => 'Marketing & Sales Director',
            'Marketing & Training Manager' => 'Marketing & Training Manager',
            'Marketing Communications Director' => 'Marketing Communications Director',
            'Marketing Communications Manager' => 'Marketing Communications Manager',
            'Marketing Coordinator' => 'Marketing Coordinator',
            'Marketing Creative Director' => 'Marketing Creative Director',
            'Marketing Director' => 'Marketing Director',
            'Marketing Manager' => 'Marketing Manager',
            'Marketing Manager, Packaging' => 'Marketing Manager, Packaging',
            'Marketing Program Manager' => 'Marketing Program Manager',
            'Marketing Project Manager' => 'Marketing Project Manager',
            'Marketing Services Manager' => 'Marketing Services Manager',
            'Marketing Specialist' => 'Marketing Specialist',
            'Marketing|Business Development Mgr' => 'Marketing|Business Development Mgr',
            'Merchandising Manager' => 'Merchandising Manager',
            'Merchandising Specialist of Color & Design' => 'Merchandising Specialist of Color & Design',
            'National Color Lab Manager' => 'National Color Lab Manager',
            'National Design Director' => 'National Design Director',
            'National Director of Specification' => 'National Director of Specification',
            'National Manager of Design' => 'National Manager of Design',
            'New Business Development Manager' => 'New Business Development Manager',
            'Owner' => 'Owner',
            'Owner/Graphic Designer' => 'Owner/Graphic Designer',
            'Owner/President' => 'Owner/President',
            'Paints & Plastics Market Manager' => 'Paints & Plastics Market Manager',
            'President' => 'President',
            'Principal' => 'Principal',
            'Principal Designer' => 'Principal Designer',
            'Principal/Director of Design' => 'Principal/Director of Design',
            'Principle' => 'Principle',
            'Product & Brand Manager' => 'Product & Brand Manager',
            'Product & Merchandising Manager' => 'Product & Merchandising Manager',
            'Product Category Manager' => 'Product Category Manager',
            'Product Coordinator' => 'Product Coordinator',
            'Product Customization Supervisor' => 'Product Customization Supervisor',
            'Product Design & Development Asst.' => 'Product Design & Development Asst.',
            'Product Design Assistant' => 'Product Design Assistant',
            'Product Design Manager' => 'Product Design Manager',
            'Product Designer' => 'Product Designer',
            'Product Development Associate Manager' => 'Product Development Associate Manager',
            'Product Development Coordinator' => 'Product Development Coordinator',
            'Product Development Manager' => 'Product Development Manager',
            'Product Manager' => 'Product Manager',
            'Product Marketing Manager' => 'Product Marketing Manager',
            'Product Planning Analyst' => 'Product Planning Analyst',
            'Project Manager' => 'Project Manager',
            'Residential Design Director' => 'Residential Design Director',
            'Sales Executive' => 'Sales Executive',
            'Senior At Director' => 'Senior At Director',
            'Senior Chemist' => 'Senior Chemist',
            'Senior CMF Designer' => 'Senior CMF Designer',
            'Senior Design Engineer' => 'Senior Design Engineer',
            'Senior Designer' => 'Senior Designer',
            'Senior Director, Global Product Manager' => 'Senior Director, Global Product Manager',
            'Senior Graphic Designer' => 'Senior Graphic Designer',
            'Senior Marketing Manager' => 'Senior Marketing Manager',
            'Senior Product Design Manager' => 'Senior Product Design Manager',
            'Senior Product Designer' => 'Senior Product Designer',
            'Senior Product Manager' => 'Senior Product Manager',
            'Senior Stylist' => 'Senior Stylist',
            'Senior Vice President, Marketing' => 'Senior Vice President, Marketing',
            'Snr Design Process Cordinator' => 'Snr Design Process Cordinator',
            'Snr Director of Design & Sales' => 'Snr Director of Design & Sales',
            'Snr Laboratory Technologist' => 'Snr Laboratory Technologist',
            'Specifications Manager' => 'Specifications Manager',
            'Sr Designer' => 'Sr Designer',
            'Sr Designer National Accounts' => 'Sr Designer National Accounts',
            'Sr Director, Product Development & Design' => 'Sr Director, Product Development & Design',
            'SR Graphic Designer' => 'SR Graphic Designer',
            'Sr Manager - Business Strategy Execution' => 'Sr Manager - Business Strategy Execution',
            'Sr Manager, Design Studio' => 'Sr Manager, Design Studio',
            'Sr Project Chemist' => 'Sr Project Chemist',
            'Sr. Director of Brand Marketing' => 'Sr. Director of Brand Marketing',
            'Sr. Director of Marketing' => 'Sr. Director of Marketing',
            'Sr. VP Product Development' => 'Sr. VP Product Development',
            'Strategic Account Director' => 'Strategic Account Director',
            'Style Product Manager' => 'Style Product Manager',
            'Supervisor - Product Design' => 'Supervisor - Product Design',
            'tbc' => 'tbc',
            'Tech Mgr Decorative Coatings' => 'Tech Mgr Decorative Coatings',
            'Technical Product Specialist' => 'Technical Product Specialist',
            'Technical Sales Director' => 'Technical Sales Director',
            'Technical Service' => 'Technical Service',
            'Textile Designer' => 'Textile Designer',
            'Textile Market Manager' => 'Textile Market Manager',
            'Textiles Product Manager' => 'Textiles Product Manager',
            'TFL Product Manager' => 'TFL Product Manager',
            'Trend Director' => 'Trend Director',
            'Trend Research Manager' => 'Trend Research Manager',
            'US Product Manager' => 'US Product Manager',
            'Variable Inc' => 'Variable Inc',
            'Vice President' => 'Vice President',
            'Vice President of Product & Marketing' => 'Vice President of Product & Marketing',
            'Vice President, Marketing' => 'Vice President, Marketing',
            'Vice President/Creative Director' => 'Vice President/Creative Director',
            'VP Client Strategy & Brand Development' => 'VP Client Strategy & Brand Development',
            'VP Marketing' => 'VP Marketing',
            'VP Marketing & Product Development' => 'VP Marketing & Product Development',
            'VP of Consumer Color Solutions' => 'VP of Consumer Color Solutions',
            'VP of Design' => 'VP of Design',
            'VP of Interior Design' => 'VP of Interior Design',
            'VP of Marketing ' => 'VP of Marketing ',
            'VP of Marketing & Product Management' => 'VP of Marketing & Product Management',
            'VP Product Development' => 'VP Product Development',
            'VP Residential Styling' => 'VP Residential Styling',
            'VP Sales & Marketing' => 'VP Sales & Marketing',
            'VP, Creative Director' => 'VP, Creative Director',
            'VP, Innovation & Worldwide Development' => 'VP, Innovation & Worldwide Development',
            'Weaveup Project Manager' => 'Weaveup Project Manager',
            'Web Content Writer/Marketing' => 'Web Content Writer/Marketing'
        );
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        $this->data['user'] = Auth::user();
        $this->data['users'] = $this->user->get()->all();
        $this->data['companies'] = $this->getCompanies();
        foreach($this->data['companies'] as $company){

            if(isset($company['members']['companyAdmin'])) {
                DB::table('companies')
                    ->where('company_name', $company['company_name'])
                    ->update([
                        'created_at' => $company['members']['companyAdmin']->created_at
                ]);
                //dd($company['members']['companyAdmin']->created_at);

            }
        }
        //dd($this->data['companies']);

        return view('admin.companymemberships')->with('data', $this->data);
    }

    public function getCompanies()
    {
        $this->data['user'] = Auth::user();

        $companies = Company::all();
        if (count($companies) < 1) {
            return $companies = [];
        }
        foreach ($companies as $company) {
            $companies2[$company->company_name] = $company->toArray();
            $members = DB::table('company_members')
                ->where('company_id', $company->id)
                ->get()->toArray();

            foreach ($members as $compmember) {
                $compmemberUser = $this->user->findOrFail($compmember->user_id);
               /* echo '<pre>';
                var_dump($compmemberUser['attributes']);
                echo '</pre>';*/
                if ($compmemberUser->companyAdmin == 1) {
                    $companies2[$company->company_name]['members']['companyAdmin'] = $compmemberUser;
                } else {
                    $companies2[$company->company_name]['members']['companyRep'][] = $compmemberUser;
                }
            }
        }
        //dd($companies2['Datacolor']);

        return $companies2;
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Company $company
     *
     * @return \Illuminate\Http\Response
     */
    public function show()
    {


    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Company $company
     *
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //add company id to session for existing user modal
        session()->put('company_id', $id);

        $company = $this->comp->findOrFail($id);
        //$this->data['users'] = $this->user->get()->all();

        $this->data['company'] = $company;

        foreach($this->data['states'] as $k=>$v) {
            if (isset($k) && isset($this->data['company']->state)){
                if ($k == $this->data['company']->state) {
                    $this->data['company']->state = $v;
                    $this->data['company']->stateabv = $k;
                }else{
                    $this->data['company']->stateabv = 'select a State';
                }
            }
        }

        foreach($this->data['country'] as $k=>$v){
            //dd($k, $this->data['companyInfo']->country);
            if(isset($k) && isset($this->data['company']->country)) {
                if ($k === $this->data['company']->country) {
                    $this->data['company']->country = $v;
                    $this->data['company']->countryabv = $k;
                } else {
                    $this->data['company']->countryabv = 'select a Country';
                }
            }
        }

        if(isset($company->consumer)) {
            $this->data['company']['consumer'] = explode(',', $company->consumer);
        }
        if(isset($company->contract)) {
            $this->data['company']['contract'] = explode(',', $company->contract);
        }

        $this->data['company']['members'] = $this->getCompanyMembers($company->id);

        //dd($this->data['company'], $id);
        $this->data['user'] = Auth::user();
        return view('admin.companiesedit')->with('data', $this->data);
    }

    public function editUpdate(Request $request, $id)
    {


        if ($request->form === 'companyupdate'){

            if (isset($request->consumer)) {
                $consumerContract = 'consumer';
                $value = implode(',', $request->consumer);
            } elseif(isset($request->contract)) {
                $consumerContract = 'contract';
                $value = implode(',', $request->contract);
            }else{
                $value = null;
                $consumerContract = 'consumer';
            }

            Validator::make($request->toArray(),[
                'compname'=>[
                    'required',
                    Rule::unique('companies')->ignore($id),
                ]
            ]);
            $this->validate($request,[
                'compaddress'   => 'required',
                'compcity'      => 'required',
                'compstate'     => 'required',
                'compzip'       => 'required',
                'compphone'     => 'required',
                'compcountry'   => 'required',
            ]);

            DB::table('companies')
                ->where('id', $id)
                ->update([
                    'company_name' => $request->compname,
                    'company_level' => $request->company_level,
                    'company_address' => $request->compaddress,
                    'city' => $request->compcity,
                    'state' => $request->compstate,
                    'zip' => $request->compzip,
                    'phone' => $request->compphone,
                    'fax' => $request->compfax,
                    'country' => $request->compcountry,
                    $consumerContract => $value,
                ]);

            session()->put('info', 'Company information updated.');
            return back();
        }elseif($request->form === 'addmember'){
            //look for username in DB and if found offer selection of user otherwise add user
            //$select_user_modal = true;

            $emailExists = DB::table('users')->where('email', $request->email)->get()->all();
            $usernameExists = DB::table('users')->where('email', $request->username)->get()->all();

            if($emailExists || $usernameExists) {

                if (count($usernameExists) > 0) {
                    $userData = DB::table('users')->where('username', $request->username)->get()->all();

                    //dd($userData);
                    session()->put('userData', $userData);
                    return back();
                }elseif (count($emailExists) > 0) {
                    $userData = DB::table('users')->where('email', $request->email)->get()->all();

                    //dd($userData);
                    session()->put('userData', $userData, 'company_id',$id);
                    return back();
                }
            }else{
                session()->put('error', 'Could not find a user with that username or email.');
                return back();
            }
        }elseif($request->form === 'removeMember'){
            //get company members
            $companyMembers = $this->getCompanyMembers($id);

            if ($companyMembers != '') {
                foreach ($companyMembers as $companyMember) {
                    $compmembersIDs[] = $companyMember->id;
                }
                if(isset($request->members)) {
                    //compare company members to submited members.
                    $diffs = array_diff($request->members, $compmembersIDs); //needs added
                    $diffs2 = array_diff($compmembersIDs, $request->members); // remove

                    if (count($diffs) > 0) {
                        //we need to add someone to the DB
                        foreach ($diffs as $diff1) {
                            DB::table('company_members')
                                ->insert([
                                    'company_id' => $id,
                                    'user_id' => $diff1,
                                ]);
                            //add company name to user record
                            DB::table('users')
                                ->where('user_id', $diff1)
                                ->update([
                                    'company' => $request->compname,
                                ]);
                            session()->put('success', 'Member has been added to the company');
                            return back();
                        }
                    } else {
                        //remvoe them from the DB.
                        foreach ($diffs2 as $diff) {
                            $remove = DB::table('company_members')
                                ->where('user_id', $diff)
                                ->get()->first();

                            if ($this->user->findOrFail($remove->user_id)->companyAdmin == 1){
                                session()->put('info', 'you can not remove the company admin.please try again.');
                                return back();
                            }else{
                                DB::table('company_members')
                                    ->where('user_id', $diff)
                                    ->delete();
                            }
                            //remove the company name from user record
                            DB::table('users')
                                ->where('id', $diff)
                                ->update([
                                    'company' => null,
                                    'lastpayment' => null,
                                ]);
                            session()->put('info', 'Member has been removed from the company.');
                            return back();
                        }
                    }
                }
            }
        }elseif($request->form === 'ChangeKeyRep'){
            //remove companyAdmin status from original Key Rep
            //find the current companyAdmin
            if($this->user->where('company', $request->company_name)->where('companyAdmin', 1)) {
                $old = $this->user->where('company', $request->company_name)
                    ->where('companyAdmin', 1)
                    ->where('id', '!=', $request->member_id)
                    ->update([
                        'companyAdmin' => 0
                    ]);
            }

            //set new selected member as Kep Rep with companyAdmin status.
            $member = $this->user->findOrFail($request->member_id);
            $member->update([
                'companyAdmin' => 1
            ]);

            return back();
        }
    }
    public function addMember(Request $request)
    {
        //dd($request);
        //add user to company member table.
        DB::table('company_members')
            ->insert([
                'user_id' => $request->user_id,
                'company_id' => $request->company_id
            ]);
        //change user's company to the new company
        //get company name
        $company = DB::table('companies')
            ->where('id', $request->company_id)
            ->value('company_name');

        DB::table('users')
            ->where('id', $request->user_id)
            ->update([
                'company' => $company,
            ]);
        session()->put('success', 'Member has succesfully been added to the company.');
        return back();
    }

    public function getCompanyMembers($id) // returns user object
    {
        $compmembers = array();
        $members = DB::table('company_members')
            ->where('company_id', $id)->get();

        foreach ($members as $member) {
            $compmembers[] = $this->user->findorfail($member->user_id);
        }
//dd($compmembers);
        return $compmembers;
    }
}