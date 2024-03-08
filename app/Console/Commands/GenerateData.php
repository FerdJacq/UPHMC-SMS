<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use Carbon\Carbon;

use Faker\Factory as Faker;

use App\Models\Customer;
use App\Models\Seller;

use App\Models\ServiceProvider;
use App\Models\Transaction;
use App\Models\TransactionDetails;
use App\Models\TransactionCustomers;
use App\Models\TransactionSummary;

use App\Http\Controllers\TransactionSummaryController;

use App\Models\Region;
use App\Models\SellerServiceProvider;

use App\Http\Controllers\FileController;

use Validator;
use DB;
use Log;
use Helper;

class GenerateData extends Command
{
    /**
      php artisan generatE:data --loop_length=1 --seller_count=100 --provider_count=5 --total_days=0 --sub_days=0 --stores_type=NON1
     */
    protected $signature = 'generate:data {--total_days=0} {--seller_count=100} {--provider_count=5} {--loop_length=1} {--sub_days=0} {--stores_type=RANDOM}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        // $status = ["PENDING", "ONGOING", "COMPLETED"];
        $status = ["COMPLETED"];
        $types = ["PRODUCT", "SERVICE"];
        $regionCodes = ["01", "02", "03", "04", "05", "06", "07", "08", "09", "10", "11", "12", "13", "14"];
        $eligibleWithheldSeller = ["NONE", "ELIGIBLE", "ACTIVE"];
        $stores_type = $this->option('stores_type');
        if ($stores_type!="NON")
        {
            $stores = [
                ["name"=>"Shop Smart", "region_code"=>"01", "type"=>"product", "vat_type"=>"NV", "registered_name"=>"Smart Retail Inc.", "registered_address"=>"123 Main St, Cityville", "business_name"=>"Shop Smart", "tin"=>"123456789", "contact_number"=>"555-1234", "email"=>"shopsmart@example.com", "seller_type"=>"INDIVIDUAL"],
                ["name"=>"Fashion Haven", "region_code"=>"01", "type"=>"product", "vat_type"=>"V", "registered_name"=>"Fashion Haven Corp.", "registered_address"=>"456 Fashion Blvd, Style City", "business_name"=>"Fashion Haven", "tin"=>"987654321", "contact_number"=>"555-5678", "email"=>"fashionhaven@example.com", "seller_type"=>"INDIVIDUAL"],
                ["name"=>"Gourmet Delights", "region_code"=>"02", "type"=>"product", "vat_type"=>"NV", "registered_name"=>"Gourmet Delights Ltd.", "registered_address"=>"789 Gourmet Ave, Foodtown", "business_name"=>"Gourmet Delights", "tin"=>"654321987", "contact_number"=>"555-4321", "email"=>"gourmetdelights@example.com", "seller_type"=>"INDIVIDUAL"],
                ["name"=>"Tech Trends", "region_code"=>"03", "type"=>"product", "vat_type"=>"V", "registered_name"=>"Tech Trends Solutions", "registered_address"=>"101 Innovation St, Techville", "business_name"=>"Tech Trends", "tin"=>"321987654", "contact_number"=>"555-8765", "email"=>"techtrends@example.com", "seller_type"=>"INDIVIDUAL"],
                ["name"=>"Home Harmony", "region_code"=>"04", "type"=>"product", "vat_type"=>"NV", "registered_name"=>"Home Harmony Homes Inc.", "registered_address"=>"456 Comfort Lane, Homestead", "business_name"=>"Home Harmony", "tin"=>"789654321", "contact_number"=>"555-9876", "email"=>"homeharmony@example.com", "seller_type"=>"INDIVIDUAL"],
                ["name"=>"Nature's Nook", "region_code"=>"05", "type"=>"product", "vat_type"=>"V", "registered_name"=>"Nature's Nook Nature Co.", "registered_address"=>"789 Greenery Rd, Nature City", "business_name"=>"Nature's Nook", "tin"=>"456789123", "contact_number"=>"555-2345", "email"=>"naturesnook@example.com", "seller_type"=>"INDIVIDUAL"],
                ["name"=>"Pet Paradise", "region_code"=>"06", "type"=>"product", "vat_type"=>"NV", "registered_name"=>"Pet Paradise LLC", "registered_address"=>"555 Pet Street, Animaland", "business_name"=>"Pet Paradise", "tin"=>"789123456", "contact_number"=>"555-8765", "email"=>"petparadise@example.com", "seller_type"=>"INDIVIDUAL"],
                ["name"=>"Books & Beyond", "region_code"=>"07", "type"=>"product", "vat_type"=>"V", "registered_name"=>"Literary Ventures Inc.", "registered_address"=>"777 Book Lane, Readville", "business_name"=>"Books & Beyond", "tin"=>"987654789", "contact_number"=>"555-4321", "email"=>"booksandbeyond@example.com", "seller_type"=>"INDIVIDUAL"],
                ["name"=>"Cosmic Crafts", "region_code"=>"08", "type"=>"product", "vat_type"=>"NV", "registered_name"=>"Cosmic Creations Ltd.", "registered_address"=>"888 Space Blvd, Galaxy City", "business_name"=>"Cosmic Crafts", "tin"=>"654789321", "contact_number"=>"555-2345", "email"=>"cosmiccrafts@example.com", "seller_type"=>"INDIVIDUAL"],
                ["name"=>"Vintage Vogue", "region_code"=>"09", "type"=>"product", "vat_type"=>"V", "registered_name"=>"Vintage Vogue Inc.", "registered_address"=>"999 Fashion Street, Retroville", "business_name"=>"Vintage Vogue", "tin"=>"321987654", "contact_number"=>"555-8765", "email"=>"vintagevogue@example.com", "seller_type"=>"INDIVIDUAL"],
                ["name"=>"Wellness Wonders", "region_code"=>"10", "type"=>"product", "vat_type"=>"NV", "registered_name"=>"Wellness World Ltd.", "registered_address"=>"101 Health Haven, Welltown", "business_name"=>"Wellness Wonders", "tin"=>"789654321", "contact_number"=>"555-1234", "email"=>"wellnesswonders@example.com", "seller_type"=>"INDIVIDUAL"],
                ["name"=>"Sports Spectrum", "region_code"=>"11", "type"=>"product", "vat_type"=>"V", "registered_name"=>"Sports Spectrum Inc.", "registered_address"=>"222 Sports Ave, Sportsville", "business_name"=>"Sports Spectrum", "tin"=>"123456789", "contact_number"=>"555-5678", "email"=>"sportsspectrum@example.com", "seller_type"=>"INDIVIDUAL"],
                ["name"=>"Electro Elegance", "region_code"=>"12", "type"=>"product", "vat_type"=>"NV", "registered_name"=>"Electric Elegance Solutions", "registered_address"=>"333 Tech Street, Electrotown", "business_name"=>"Electro Elegance", "tin"=>"987654321", "contact_number"=>"555-8765", "email"=>"electroelegance@example.com", "seller_type"=>"INDIVIDUAL"],
                ["name"=>"Toy Time", "region_code"=>"13", "type"=>"product", "vat_type"=>"V", "registered_name"=>"Toy Time Toys Ltd.", "registered_address"=>"444 Toy Lane, Playville", "business_name"=>"Toy Time", "tin"=>"321987654", "contact_number"=>"555-2345", "email"=>"toytime@example.com", "seller_type"=>"INDIVIDUAL"],
                ["name"=>"Jewel Junction", "region_code"=>"14", "type"=>"product", "vat_type"=>"NV", "registered_name"=>"Gem Treasures LLC", "registered_address"=>"567 Gemstone Lane, Precious City", "business_name"=>"Jewel Junction", "tin"=>"654789321", "contact_number"=>"555-7890", "email"=>"jeweljunction@example.com", "seller_type"=>"INDIVIDUAL"],
                ["name"=>"Culinary Canvas", "region_code"=>"01", "type"=>"service", "vat_type"=>"V", "registered_name"=>"Taste Palette Catering", "registered_address"=>"789 Culinary St, Flavor Town", "business_name"=>"Culinary Canvas", "tin"=>"987654789", "contact_number"=>"555-0987", "email"=>"culinarycanvas@example.com", "seller_type"=>"INDIVIDUAL"],
                ["name"=>"Artistic Attire", "region_code"=>"01", "type"=>"product", "vat_type"=>"NV", "registered_name"=>"Creative Couture Inc.", "registered_address"=>"101 Artistic Blvd, Design City", "business_name"=>"Artistic Attire", "tin"=>"456123987", "contact_number"=>"555-8765", "email"=>"artisticattire@example.com", "seller_type"=>"INDIVIDUAL"],
                ["name"=>"Tool Time", "region_code"=>"02", "type"=>"product", "vat_type"=>"V", "registered_name"=>"Handy Hardware Solutions", "registered_address"=>"222 Tool Lane, Toolbox", "business_name"=>"Tool Time", "tin"=>"321789456", "contact_number"=>"555-2345", "email"=>"tooltime@example.com", "seller_type"=>"INDIVIDUAL"],
                ["name"=>"Organic Oasis", "region_code"=>"03", "type"=>"product", "vat_type"=>"NV", "registered_name"=>"Organic Harvest Ltd.", "registered_address"=>"333 Green Grove, Pureland", "business_name"=>"Organic Oasis", "tin"=>"123987456", "contact_number"=>"555-5678", "email"=>"organicoasis@example.com", "seller_type"=>"INDIVIDUAL"],
                ["name"=>"Music Magic", "region_code"=>"04", "type"=>"product", "vat_type"=>"V", "registered_name"=>"Melody Masters Inc.", "registered_address"=>"444 Harmony Street, Musictown", "business_name"=>"Music Magic", "tin"=>"789321654", "contact_number"=>"555-9876", "email"=>"musicmagic@example.com", "seller_type"=>"INDIVIDUAL"],
                ["name"=>"Essential Emporium", "region_code"=>"05", "type"=>"product", "vat_type"=>"NV", "registered_name"=>"Essential Elegance Corp.", "registered_address"=>"555 Essential Ave, Necessity City", "business_name"=>"Essential Emporium", "tin"=>"987456321", "contact_number"=>"555-2345", "email"=>"essentialemporium@example.com", "seller_type"=>"INDIVIDUAL"],
                ["name"=>"Fitness Fusion", "region_code"=>"06", "type"=>"service", "vat_type"=>"V", "registered_name"=>"FitForm Solutions LLC", "registered_address"=>"666 Health Lane, Fitville", "business_name"=>"Fitness Fusion", "tin"=>"321654987", "contact_number"=>"555-8765", "email"=>"fitnessfusion@example.com", "seller_type"=>"INDIVIDUAL"],
                ["name"=>"Marvelous Munchies", "region_code"=>"07", "type"=>"product", "vat_type"=>"NV", "registered_name"=>"Munchie Marvels Inc.", "registered_address"=>"777 Tasty Blvd, Snacksville", "business_name"=>"Marvelous Munchies", "tin"=>"654789012", "contact_number"=>"555-7654", "email"=>"munchiemarvels@example.com", "seller_type"=>"INDIVIDUAL"],
                ["name"=>"Urban Umbrella", "region_code"=>"08", "type"=>"product", "vat_type"=>"V", "registered_name"=>"City Shelter Co.", "registered_address"=>"888 Rainy Street, Umbrellatown", "business_name"=>"Urban Umbrella", "tin"=>"987012345", "contact_number"=>"555-8901", "email"=>"urbanumbrella@example.com", "seller_type"=>"INDIVIDUAL"],
                ["name"=>"Global Groove", "region_code"=>"09", "type"=>"product", "vat_type"=>"NV", "registered_name"=>"Global Groove Enterprises", "registered_address"=>"999 Groovy Ave, Worldbeat City", "business_name"=>"Global Groove", "tin"=>"012345678", "contact_number"=>"555-6789", "email"=>"globalgroove@example.com", "seller_type"=>"INDIVIDUAL"],
                ["name"=>"Silver Lining", "region_code"=>"10", "type"=>"service", "vat_type"=>"V", "registered_name"=>"Silver Lining Solutions", "registered_address"=>"101 Cloud Street, Positivetown", "business_name"=>"Silver Lining", "tin"=>"876543210", "contact_number"=>"555-0123", "email"=>"silverlining@example.com", "seller_type"=>"INDIVIDUAL"],
                ["name"=>"Sweet Symphony", "region_code"=>"11", "type"=>"service", "vat_type"=>"NV", "registered_name"=>"Symphony Sweets Co.", "registered_address"=>"222 Harmony Lane, Melodyville", "business_name"=>"Sweet Symphony", "tin"=>"109876543", "contact_number"=>"555-3456", "email"=>"sweetsymphony@example.com", "seller_type"=>"INDIVIDUAL"],
                ["name"=>"Safari Styles", "region_code"=>"12", "type"=>"product", "vat_type"=>"V", "registered_name"=>"Safari Styles Ltd.", "registered_address"=>"333 Wilderness Ave, Adventuretown", "business_name"=>"Safari Styles", "tin"=>"432109876", "contact_number"=>"555-6789", "email"=>"safaristyles@example.com", "seller_type"=>"INDIVIDUAL"],
                ["name"=>"Luxury Lane", "region_code"=>"13", "type"=>"product", "vat_type"=>"NV", "registered_name"=>"Luxury Lane Enterprises", "registered_address"=>"444 Opulence Street, Richville", "business_name"=>"Luxury Lane", "tin"=>"567890123", "contact_number"=>"555-0123", "email"=>"luxurylane@example.com", "seller_type"=>"INDIVIDUAL"],
                ["name"=>"Outdoor Oasis", "region_code"=>"14", "type"=>"product", "vat_type"=>"V", "registered_name"=>"Outdoor Oasis Inc.", "registered_address"=>"567 Nature Lane, Outdoorsville", "business_name"=>"Outdoor Oasis", "tin"=>"901234567", "contact_number"=>"555-3456", "email"=>"outdooroasis@example.com", "seller_type"=>"INDIVIDUAL"],
                ["name"=>"Eco Elegance", "region_code"=>"01", "type"=>"service", "vat_type"=>"NV", "registered_name"=>"Eco Elegance Solutions", "registered_address"=>"789 Green Street, Ecotown", "business_name"=>"Eco Elegance", "tin"=>"890123456", "contact_number"=>"555-6789", "email"=>"ecoelegance@example.com", "seller_type"=>"INDIVIDUAL"],
                ["name"=>"Floral Fantasy", "region_code"=>"01", "type"=>"product", "vat_type"=>"V", "registered_name"=>"Blossom Blooms Co.", "registered_address"=>"101 Flower Blvd, Bloomington", "business_name"=>"Floral Fantasy", "tin"=>"567890123", "contact_number"=>"555-0123", "email"=>"floralfantasy@example.com", "seller_type"=>"INDIVIDUAL"],
                ["name"=>"Digital Den", "region_code"=>"02", "type"=>"product", "vat_type"=>"NV", "registered_name"=>"Digital Den Solutions", "registered_address"=>"222 Tech Street, Digitown", "business_name"=>"Digital Den", "tin"=>"345678901", "contact_number"=>"555-7890", "email"=>"digitalden@example.com", "seller_type"=>"INDIVIDUAL"],
                ["name"=>"Crafty Corner", "region_code"=>"03", "type"=>"service", "vat_type"=>"V", "registered_name"=>"Crafty Creations Co.", "registered_address"=>"333 Artistic Ave, Craftsville", "business_name"=>"Crafty Corner", "tin"=>"456789012", "contact_number"=>"555-0123", "email"=>"craftycorner@example.com", "seller_type"=>"INDIVIDUAL"],
                ["name"=>"Sunny Seeds", "region_code"=>"04", "type"=>"product", "vat_type"=>"NV", "registered_name"=>"Sunny Seeds Farms Ltd.", "registered_address"=>"444 Sunshine Blvd, Seedstown", "business_name"=>"Sunny Seeds", "tin"=>"567890123", "contact_number"=>"555-3456", "email"=>"sunnyseeds@example.com", "seller_type"=>"INDIVIDUAL"],
                ["name"=>"Sculpted Spaces", "region_code"=>"05", "type"=>"service", "vat_type"=>"V", "registered_name"=>"Sculpted Spaces Interiors", "registered_address"=>"555 Art Street, Sculptureville", "business_name"=>"Sculpted Spaces", "tin"=>"678901234", "contact_number"=>"555-6789", "email"=>"sculptedspaces@example.com", "seller_type"=>"INDIVIDUAL"],
                ["name"=>"Innovative Interiors", "region_code"=>"06", "type"=>"product", "vat_type"=>"NV", "registered_name"=>"Innovative Interiors Ltd.", "registered_address"=>"666 Design Lane, Innovation City", "business_name"=>"Innovative Interiors", "tin"=>"789012345", "contact_number"=>"555-0123", "email"=>"innovativeinteriors@example.com", "seller_type"=>"INDIVIDUAL"],
                ["name"=>"Joyful Journeys", "region_code"=>"07", "type"=>"product", "vat_type"=>"V", "registered_name"=>"Joyful Journeys Travel Co.", "registered_address"=>"777 Adventure Ave, Traveltown", "business_name"=>"Joyful Journeys", "tin"=>"890123456", "contact_number"=>"555-3456", "email"=>"joyfuljourneys@example.com", "seller_type"=>"INDIVIDUAL"],
                ["name"=>"Clever Creations", "region_code"=>"08", "type"=>"product", "vat_type"=>"NV", "registered_name"=>"Clever Creations Ltd.", "registered_address"=>"888 Innovation St, Cleverville", "business_name"=>"Clever Creations", "tin"=>"901234567", "contact_number"=>"555-6789", "email"=>"clevercreations@example.com", "seller_type"=>"INDIVIDUAL"],
                ["name"=>"Blissful Bites", "region_code"=>"09", "type"=>"product", "vat_type"=>"V", "registered_name"=>"Blissful Bites Catering", "registered_address"=>"999 Culinary Blvd, Blissville", "business_name"=>"Blissful Bites", "tin"=>"012345678", "contact_number"=>"555-0123", "email"=>"blissfulbites@example.com", "seller_type"=>"INDIVIDUAL"],
                ["name"=>"Wondrous Waves", "region_code"=>"10", "type"=>"product", "vat_type"=>"NV", "registered_name"=>"Wondrous Waves Surf Co.", "registered_address"=>"101 Surf Lane, Wavetown", "business_name"=>"Wondrous Waves", "tin"=>"123456789", "contact_number"=>"555-3456", "email"=>"wondrouswaves@example.com", "seller_type"=>"INDIVIDUAL"],
                ["name"=>"Heritage Haven", "region_code"=>"11", "type"=>"service", "vat_type"=>"V", "registered_name"=>"Heritage Haven Tours", "registered_address"=>"222 Heritage Street, Heritagetown", "business_name"=>"Heritage Haven", "tin"=>"234567890", "contact_number"=>"555-6789", "email"=>"heritagehaven@example.com", "seller_type"=>"INDIVIDUAL"],
                ["name"=>"Aromatic Ambiance", "region_code"=>"12", "type"=>"product", "vat_type"=>"NV", "registered_name"=>"Aromatic Ambiance Inc.", "registered_address"=>"333 Fragrance Blvd, Scentville", "business_name"=>"Aromatic Ambiance", "tin"=>"345678901", "contact_number"=>"555-0123", "email"=>"aromaticambiance@example.com", "seller_type"=>"INDIVIDUAL"],
                ["name"=>"Golden Groves", "region_code"=>"13", "type"=>"product", "vat_type"=>"V", "registered_name"=>"Golden Groves Farms Ltd.", "registered_address"=>"444 Orchard Lane, Goldentown", "business_name"=>"Golden Groves", "tin"=>"456789012", "contact_number"=>"555-3456", "email"=>"goldengroves@example.com", "seller_type"=>"INDIVIDUAL"],
                ["name"=>"Chic Charms", "region_code"=>"14", "type"=>"product", "vat_type"=>"NV", "registered_name"=>"Chic Charms Jewelry Co.", "registered_address"=>"555 Elegance Ave, Styleville", "business_name"=>"Chic Charms", "tin"=>"567890123", "contact_number"=>"555-6789", "email"=>"chiccharms@example.com", "seller_type"=>"INDIVIDUAL"],
                ["name"=>"Celestial Ceramics", "region_code"=>"01", "type"=>"service", "vat_type"=>"V", "registered_name"=>"Celestial Ceramics Studio", "registered_address"=>"789 Arts Street, Celestial City", "business_name"=>"Celestial Ceramics", "tin"=>"678901234", "contact_number"=>"555-0123", "email"=>"celestialceramics@example.com", "seller_type"=>"CORP"],
                ["name"=>"Adventure Awaits", "region_code"=>"01", "type"=>"service", "vat_type"=>"NV", "registered_name"=>"Adventure Awaits Travel Co.", "registered_address"=>"101 Explorer Ave, Adventurertown", "business_name"=>"Adventure Awaits", "tin"=>"789012345", "contact_number"=>"555-6789", "email"=>"adventureawaits@example.com", "seller_type"=>"CORP"],
                ["name"=>"Dazzling Designs", "region_code"=>"02", "type"=>"product", "vat_type"=>"V", "registered_name"=>"Dazzling Designs Ltd.", "registered_address"=>"222 Design Lane, Glitter City", "business_name"=>"Dazzling Designs", "tin"=>"890123456", "contact_number"=>"555-0123", "email"=>"dazzlingdesigns@example.com", "seller_type"=>"CORP"],
                ["name"=>"Bountiful Blessings", "region_code"=>"03", "type"=>"product", "vat_type"=>"NV", "registered_name"=>"Bountiful Blessings Farms Ltd.", "registered_address"=>"333 Blessings Blvd, Gratitudeville", "business_name"=>"Bountiful Blessings", "tin"=>"901234567", "contact_number"=>"555-3456", "email"=>"bountifulblessings@example.com", "seller_type"=>"CORP"],
                ["name"=>"Dynamic Dreams", "region_code"=>"04", "type"=>"product", "vat_type"=>"V", "registered_name"=>"Dynamic Dreams Inc.", "registered_address"=>"444 Dream Street, Dreamville", "business_name"=>"Dynamic Dreams", "tin"=>"012345678", "contact_number"=>"555-6789", "email"=>"dynamicdreams@example.com", "seller_type"=>"CORP"],
                ["name"=>"Pampered Paws", "region_code"=>"05", "type"=>"product", "vat_type"=>"NV", "registered_name"=>"Pampered Paws Pet Care", "registered_address"=>"555 Pet Lane, Petville", "business_name"=>"Pampered Paws", "tin"=>"123456789", "contact_number"=>"555-0123", "email"=>"pamperedpaws@example.com", "seller_type"=>"CORP"],
                ["name"=>"Zen Zephyr", "region_code"=>"06", "type"=>"product", "vat_type"=>"V", "registered_name"=>"Zen Zephyr Wellness", "registered_address"=>"666 Serenity St, Zen City", "business_name"=>"Zen Zephyr", "tin"=>"234567890", "contact_number"=>"555-3456", "email"=>"zenzephyr@example.com", "seller_type"=>"CORP"],
                ["name"=>"Epicurean Essence", "region_code"=>"07", "type"=>"product", "vat_type"=>"NV", "registered_name"=>"Epicurean Essence Co.", "registered_address"=>"777 Gourmet Lane, Epicureatown", "business_name"=>"Epicurean Essence", "tin"=>"345678901", "contact_number"=>"555-6789", "email"=>"epicureanessence@example.com", "seller_type"=>"CORP"],
                ["name"=>"Serene Senses", "region_code"=>"08", "type"=>"product", "vat_type"=>"V", "registered_name"=>"Serene Senses Spa", "registered_address"=>"888 Tranquil Blvd, Serenityville", "business_name"=>"Serene Senses", "tin"=>"456789012", "contact_number"=>"555-0123", "email"=>"serenesenses@example.com", "seller_type"=>"CORP"],
                ["name"=>"Rustic Radiance", "region_code"=>"09", "type"=>"product", "vat_type"=>"NV", "registered_name"=>"Rustic Radiance Crafts", "registered_address"=>"999 Rustic Ave, Rusticville", "business_name"=>"Rustic Radiance", "tin"=>"567890123", "contact_number"=>"555-3456", "email"=>"rusticradiance@example.com", "seller_type"=>"CORP"],
                ["name"=>"Allure Alchemy", "region_code"=>"10", "type"=>"product", "vat_type"=>"V", "registered_name"=>"Allure Alchemy Creations", "registered_address"=>"101 Allure Lane, Enchantment City", "business_name"=>"Allure Alchemy", "tin"=>"678901234", "contact_number"=>"555-6789", "email"=>"allurealchemy@example.com", "seller_type"=>"CORP"],
                ["name"=>"Miracle Makers", "region_code"=>"11", "type"=>"service", "vat_type"=>"NV", "registered_name"=>"Miracle Makers Foundation", "registered_address"=>"222 Miracle Blvd, Miracleville", "business_name"=>"Miracle Makers", "tin"=>"789012345", "contact_number"=>"555-0123", "email"=>"miraclemakers@example.com", "seller_type"=>"CORP"],
                ["name"=>"Vibrant Ventures", "region_code"=>"12", "type"=>"product", "vat_type"=>"V", "registered_name"=>"Vibrant Ventures Inc.", "registered_address"=>"333 Vibrant Street, Colorful City", "business_name"=>"Vibrant Ventures", "tin"=>"890123456", "contact_number"=>"555-3456", "email"=>"vibrantventures@example.com", "seller_type"=>"CORP"],
                ["name"=>"Silk & Spice", "region_code"=>"13", "type"=>"product", "vat_type"=>"NV", "registered_name"=>"Silk & Spice Textiles", "registered_address"=>"444 Silk Lane, Spicetown", "business_name"=>"Silk & Spice", "tin"=>"901234567", "contact_number"=>"555-6789", "email"=>"silkandspice@example.com", "seller_type"=>"CORP"],
                ["name"=>"Prismatic Pleasures", "region_code"=>"14", "type"=>"product", "vat_type"=>"V", "registered_name"=>"Prismatic Pleasures Co.", "registered_address"=>"555 Prism Ave, Colorville", "business_name"=>"Prismatic Pleasures", "tin"=>"012345678", "contact_number"=>"555-0123", "email"=>"prismaticpleasures@example.com", "seller_type"=>"CORP"],
                ["name"=>"Quirky Quarters", "region_code"=>"01", "type"=>"service", "vat_type"=>"NV", "registered_name"=>"Quirky Quarters Design Studio", "registered_address"=>"789 Quirky Street, Quirktown", "business_name"=>"Quirky Quarters", "tin"=>"234567890", "contact_number"=>"555-3456", "email"=>"quirkyquarters@example.com", "seller_type"=>"CORP"],
                ["name"=>"Elite Elegance", "region_code"=>"01", "type"=>"product", "vat_type"=>"V", "registered_name"=>"Elite Elegance Couture", "registered_address"=>"101 Elegance Blvd, Elitetown", "business_name"=>"Elite Elegance", "tin"=>"345678901", "contact_number"=>"555-6789", "email"=>"eliteelegance@example.com", "seller_type"=>"CORP"],
                ["name"=>"Glowing Gardens", "region_code"=>"02", "type"=>"product", "vat_type"=>"NV", "registered_name"=>"Glowing Gardens Landscapes", "registered_address"=>"222 Garden Street, Glowville", "business_name"=>"Glowing Gardens", "tin"=>"456789012", "contact_number"=>"555-0123", "email"=>"glowinggardens@example.com", "seller_type"=>"CORP"],
                ["name"=>"Savvy Sips", "region_code"=>"03", "type"=>"service", "vat_type"=>"V", "registered_name"=>"Savvy Sips Beverages", "registered_address"=>"333 Savvy Lane, Sipstown", "business_name"=>"Savvy Sips", "tin"=>"567890123", "contact_number"=>"555-3456", "email"=>"savvysips@example.com", "seller_type"=>"CORP"],
                ["name"=>"Ambrosial Attic", "region_code"=>"04", "type"=>"product", "vat_type"=>"NV", "registered_name"=>"Ambrosial Attic Co.", "registered_address"=>"444 Ambrosia Ave, Foodville", "business_name"=>"Ambrosial Attic", "tin"=>"678901234", "contact_number"=>"555-6789", "email"=>"ambrosialattic@example.com", "seller_type"=>"CORP"],
                ["name"=>"Harmonic Hues", "region_code"=>"05", "type"=>"product", "vat_type"=>"V", "registered_name"=>"Harmonic Hues Inc.", "registered_address"=>"555 Palette Lane, Artville", "business_name"=>"Harmonic Hues", "tin"=>"789012345", "contact_number"=>"555-0123", "email"=>"harmonichues@example.com", "seller_type"=>"CORP"],
                ["name"=>"Plush Paws", "region_code"=>"06", "type"=>"product", "vat_type"=>"NV", "registered_name"=>"Plush Paws Pet Boutique", "registered_address"=>"666 Pet Street, Plushville", "business_name"=>"Plush Paws", "tin"=>"890123456", "contact_number"=>"555-3456", "email"=>"plushpaws@example.com", "seller_type"=>"CORP"],
                ["name"=>"Funky Fables", "region_code"=>"07", "type"=>"service", "vat_type"=>"V", "registered_name"=>"Funky Fables Bookstore", "registered_address"=>"777 Story Blvd, Talestown", "business_name"=>"Funky Fables", "tin"=>"901234567", "contact_number"=>"555-6789", "email"=>"funkyfables@example.com", "seller_type"=>"CORP"],
                ["name"=>"Fiesta Fashions", "region_code"=>"08", "type"=>"product", "vat_type"=>"NV", "registered_name"=>"Fiesta Fashions Co.", "registered_address"=>"888 Fiesta Ave, Styletown", "business_name"=>"Fiesta Fashions", "tin"=>"012345678", "contact_number"=>"555-0123", "email"=>"fiestafashions@example.com", "seller_type"=>"CORP"],
                ["name"=>"Nautical Nectar", "region_code"=>"09", "type"=>"product", "vat_type"=>"V", "registered_name"=>"Nautical Nectar Beverages", "registered_address"=>"999 Nautical Lane, Marinatown", "business_name"=>"Nautical Nectar", "tin"=>"123456789", "contact_number"=>"555-3456", "email"=>"nauticalnectar@example.com", "seller_type"=>"CORP"],
                ["name"=>"Crystal Chronicles", "region_code"=>"10", "type"=>"product", "vat_type"=>"NV", "registered_name"=>"Crystal Chronicles Jewelry", "registered_address"=>"101 Crystal Blvd, Gemstown", "business_name"=>"Crystal Chronicles", "tin"=>"234567890", "contact_number"=>"555-6789", "email"=>"crystalchronicles@example.com", "seller_type"=>"CORP"],
                ["name"=>"Velvet Vista", "region_code"=>"11", "type"=>"service", "vat_type"=>"V", "registered_name"=>"Velvet Vista Photography", "registered_address"=>"222 Velvet Street, Vistatown", "business_name"=>"Velvet Vista", "tin"=>"345678901", "contact_number"=>"555-0123", "email"=>"velvetvista@example.com", "seller_type"=>"CORP"],
                ["name"=>"Ethereal Eclat", "region_code"=>"12", "type"=>"product", "vat_type"=>"NV", "registered_name"=>"Ethereal Eclat Creations", "registered_address"=>"333 Ethereal Ave, Dreamland", "business_name"=>"Ethereal Eclat", "tin"=>"456789012", "contact_number"=>"555-3456", "email"=>"etherealeclat@example.com", "seller_type"=>"CORP"],
                ["name"=>"Charm City", "region_code"=>"13", "type"=>"product", "vat_type"=>"V", "registered_name"=>"Charm City Accessories", "registered_address"=>"444 Charm Lane, Enchantville", "business_name"=>"Charm City", "tin"=>"567890123", "contact_number"=>"555-6789", "email"=>"charmcity@example.com", "seller_type"=>"CORP"],
                ["name"=>"Emerald Echo", "region_code"=>"14", "type"=>"product", "vat_type"=>"NV", "registered_name"=>"Emerald Echo Jewelry Co.", "registered_address"=>"555 Gem Ave, Jewelstown", "business_name"=>"Emerald Echo", "tin"=>"678901234", "contact_number"=>"555-0123", "email"=>"emeraldecho@example.com", "seller_type"=>"CORP"],
                ["name"=>"Trendy Trinkets", "region_code"=>"01", "type"=>"service", "vat_type"=>"V", "registered_name"=>"Trendy Trinkets Boutique", "registered_address"=>"789 Trendy Street, Chicville", "business_name"=>"Trendy Trinkets", "tin"=>"890123456", "contact_number"=>"555-3456", "email"=>"trendytrinkets@example.com", "seller_type"=>"CORP"],
                ["name"=>"Grandeur Glimpse", "region_code"=>"01", "type"=>"product", "vat_type"=>"NV", "registered_name"=>"Grandeur Glimpse Furniture", "registered_address"=>"101 Grand Blvd, Grandtown", "business_name"=>"Grandeur Glimpse", "tin"=>"901234567", "contact_number"=>"555-6789", "email"=>"grandeurglimpse@example.com", "seller_type"=>"CORP"],
                ["name"=>"Majestic Morsels", "region_code"=>"02", "type"=>"product", "vat_type"=>"NV", "registered_name"=>"Majestic Morsels Bakery", "registered_address"=>"222 Bakery Street, Delicioustown", "business_name"=>"Majestic Morsels", "tin"=>"345678901", "contact_number"=>"555-0123", "email"=>"majesticmorsels@example.com", "seller_type"=>"CORP"],
                ["name"=>"Epic Elements", "region_code"=>"03", "type"=>"service", "vat_type"=>"V", "registered_name"=>"Epic Elements Design Studio", "registered_address"=>"333 Element Lane, Stylish City", "business_name"=>"Epic Elements", "tin"=>"456789012", "contact_number"=>"555-3456", "email"=>"epicelements@example.com", "seller_type"=>"CORP"],
                ["name"=>"Joyous Junction", "region_code"=>"04", "type"=>"product", "vat_type"=>"NV", "registered_name"=>"Joyous Junction Toys", "registered_address"=>"444 Joyful Ave, Playtown", "business_name"=>"Joyous Junction", "tin"=>"567890123", "contact_number"=>"555-6789", "email"=>"joyousjunction@example.com", "seller_type"=>"CORP"],
                ["name"=>"Tropical Trends", "region_code"=>"05", "type"=>"product", "vat_type"=>"V", "registered_name"=>"Tropical Trends Clothing", "registered_address"=>"555 Tropical Lane, Fashionville", "business_name"=>"Tropical Trends", "tin"=>"678901234", "contact_number"=>"555-0123", "email"=>"tropicaltrends@example.com", "seller_type"=>"CORP"],
                ["name"=>"Platinum Panache", "region_code"=>"06", "type"=>"product", "vat_type"=>"NV", "registered_name"=>"Platinum Panache Jewelry", "registered_address"=>"666 Panache Street, Luxetown", "business_name"=>"Platinum Panache", "tin"=>"789012345", "contact_number"=>"555-3456", "email"=>"platinumpanache@example.com", "seller_type"=>"CORP"],
                ["name"=>"Cascade Couture", "region_code"=>"07", "type"=>"service", "vat_type"=>"V", "registered_name"=>"Cascade Couture Fashion House", "registered_address"=>"777 Couture Blvd, Elegancetown", "business_name"=>"Cascade Couture", "tin"=>"890123456", "contact_number"=>"555-6789", "email"=>"cascadecouture@example.com", "seller_type"=>"CORP"],
                ["name"=>"Moonlit Marvels", "region_code"=>"08", "type"=>"product", "vat_type"=>"NV", "registered_name"=>"Moonlit Marvels Home Decor", "registered_address"=>"888 Marvel Lane, Dreamville", "business_name"=>"Moonlit Marvels", "tin"=>"901234567", "contact_number"=>"555-0123", "email"=>"moonlitmarvels@example.com", "seller_type"=>"CORP"],
                ["name"=>"Spicy Splendors", "region_code"=>"09", "type"=>"product", "vat_type"=>"V", "registered_name"=>"Spicy Splendors Gourmet", "registered_address"=>"999 Spice Street, Flavortown", "business_name"=>"Spicy Splendors", "tin"=>"012345678", "contact_number"=>"555-3456", "email"=>"spicysplendors@example.com", "seller_type"=>"CORP"],
                ["name"=>"Tranquil Treasures", "region_code"=>"10", "type"=>"product", "vat_type"=>"NV", "registered_name"=>"Tranquil Treasures Art Gallery", "registered_address"=>"101 Tranquil Blvd, Artistic City", "business_name"=>"Tranquil Treasures", "tin"=>"123456789", "contact_number"=>"555-6789", "email"=>"tranquiltreasures@example.com", "seller_type"=>"CORP"],
                ["name"=>"Pristine Picks", "region_code"=>"11", "type"=>"service", "vat_type"=>"V", "registered_name"=>"Pristine Picks Antiques", "registered_address"=>"222 Antique Street, Collectortown", "business_name"=>"Pristine Picks", "tin"=>"234567890", "contact_number"=>"555-0123", "email"=>"pristinepicks@example.com", "seller_type"=>"CORP"],
                ["name"=>"Mystic Mingle", "region_code"=>"12", "type"=>"product", "vat_type"=>"NV", "registered_name"=>"Mystic Mingle Occult Shop", "registered_address"=>"333 Mystic Ave, Mystictown", "business_name"=>"Mystic Mingle", "tin"=>"345678901", "contact_number"=>"555-3456", "email"=>"mysticmingle@example.com", "seller_type"=>"CORP"],
                ["name"=>"Infinite Instincts", "region_code"=>"13", "type"=>"product", "vat_type"=>"V", "registered_name"=>"Infinite Instincts Health Store", "registered_address"=>"444 Infinite Lane, Wellnessville", "business_name"=>"Infinite Instincts", "tin"=>"456789012", "contact_number"=>"555-6789", "email"=>"infiniteinstincts@example.com", "seller_type"=>"CORP"],
                ["name"=>"Splendid Solace", "region_code"=>"14", "type"=>"product", "vat_type"=>"NV", "registered_name"=>"Splendid Solace Spa", "registered_address"=>"555 Solace Ave, Serenitytown", "business_name"=>"Splendid Solace", "tin"=>"567890123", "contact_number"=>"555-0123", "email"=>"splendidsolace@example.com", "seller_type"=>"CORP"],
                ["name"=>"Lush Labyrinth", "region_code"=>"01", "type"=>"service", "vat_type"=>"V", "registered_name"=>"Lush Labyrinth Floral Studio", "registered_address"=>"789 Lush Street, Floratown", "business_name"=>"Lush Labyrinth", "tin"=>"678901234", "contact_number"=>"555-3456", "email"=>"lushlabyrinth@example.com", "seller_type"=>"CORP"],
                ["name"=>"Manila Boutique Finds", "region_code"=>"11", "type"=>"service", "vat_type"=>"V", "registered_name"=>"Manila Boutique Finds", "registered_address"=>"222 Antique Street, Collectortown", "business_name"=>"Manila Boutique Finds", "tin"=>"2345678901", "contact_number"=>"555-0123", "email"=>"pristinepicks@example.com", "seller_type"=>"CORP"],
                ["name"=>"Palm Haven Mart", "region_code"=>"12", "type"=>"product", "vat_type"=>"NV", "registered_name"=>"Palm Haven Mart Occult Shop", "registered_address"=>"333 Mystic Ave, Mystictown", "business_name"=>"Palm Haven Mart", "tin"=>"3456789012", "contact_number"=>"555-3456", "email"=>"mysticmingle@example.com", "seller_type"=>"CORP"],
                ["name"=>"Island Chic Collectibles", "region_code"=>"13", "type"=>"product", "vat_type"=>"V", "registered_name"=>"Island Chic Collectibles Health Store", "registered_address"=>"444 Infinite Lane, Wellnessville", "business_name"=>"Island Chic Collectibles", "tin"=>"4567890123", "contact_number"=>"555-6789", "email"=>"infiniteinstincts@example.com", "seller_type"=>"CORP"],
                ["name"=>"Metro Spice Bazaar", "region_code"=>"14", "type"=>"product", "vat_type"=>"NV", "registered_name"=>"Metro Spice Bazaar Spa", "registered_address"=>"555 Solace Ave, Serenitytown", "business_name"=>"Metro Spice Bazaar", "tin"=>"5678901234", "contact_number"=>"555-0123", "email"=>"splendidsolace@example.com", "seller_type"=>"CORP"],
                ["name"=>"Tropical Treasures Emporium", "region_code"=>"01", "type"=>"service", "vat_type"=>"V", "registered_name"=>"Tropical Treasures Emporium Floral Studio", "registered_address"=>"789 Lush Street, Floratown", "business_name"=>"Tropical Treasures Emporium", "tin"=>"6789012345", "contact_number"=>"555-3456", "email"=>"lushlabyrinth@example.com", "seller_type"=>"CORP"],
            ];
        
        }
        else 
        {
            $stores = [
                ["name"=>"Mystic Treasures Haven", "region_code"=>"01", "type"=>"product", "vat_type"=>"V", "registered_name"=>"Mystic Treasures Haven", "registered_address"=>"123 Enchanting Lane, Mystictown", "business_name"=>"Mystic Treasures Haven", "tin"=>"7890123456", "contact_number"=>"555-7890", "email"=>"mystictreasures@example.com", "seller_type"=>"INDIVIDUAL"],
                ["name"=>"Tranquil Essence Creations", "region_code"=>"02", "type"=>"service", "vat_type"=>"V", "registered_name"=>"Tranquil Essence Creations", "registered_address"=>"222 Peaceful Street, Serenestown", "business_name"=>"Tranquil Essence Creations", "tin"=>"8901234567", "contact_number"=>"555-4567", "email"=>"tranquilessence@example.com", "seller_type"=>"CORP"],
                ["name"=>"Zenith Zephyr Emporium", "region_code"=>"03", "type"=>"product", "vat_type"=>"NV", "registered_name"=>"Zenith Zephyr Emporium", "registered_address"=>"333 Zephyr Lane, Zephyrtown", "business_name"=>"Zenith Zephyr Emporium", "tin"=>"9012345678", "contact_number"=>"555-2345", "email"=>"zenithzephyr@example.com", "seller_type"=>"CORP"],
                ["name"=>"Ethereal Harmony Bazaar", "region_code"=>"04", "type"=>"product", "vat_type"=>"V", "registered_name"=>"Ethereal Harmony Bazaar", "registered_address"=>"444 Harmony Street, Etherealtown", "business_name"=>"Ethereal Harmony Bazaar", "tin"=>"0123456789", "contact_number"=>"555-5678", "email"=>"etherealharmony@example.com", "seller_type"=>"INDIVIDUAL"],
                ["name"=>"Celestial Bloom Creations", "region_code"=>"05", "type"=>"service", "vat_type"=>"NV", "registered_name"=>"Celestial Bloom Creations", "registered_address"=>"555 Celestial Lane, Bloomville", "business_name"=>"Celestial Bloom Creations", "tin"=>"3456789012", "contact_number"=>"555-6789", "email"=>"celestialbloom@example.com", "seller_type"=>"INDIVIDUAL"],
                ["name"=>"Serenity Springs Mart", "region_code"=>"06", "type"=>"product", "vat_type"=>"NV", "registered_name"=>"Serenity Springs Mart", "registered_address"=>"666 Serenity Street, Springtown", "business_name"=>"Serenity Springs Mart", "tin"=>"4567890123", "contact_number"=>"555-8901", "email"=>"serenitysprings@example.com", "seller_type"=>"CORP"],
                ["name"=>"Cosmic Bloom Emporium", "region_code"=>"07", "type"=>"service", "vat_type"=>"V", "registered_name"=>"Cosmic Bloom Emporium", "registered_address"=>"777 Cosmic Lane, Bloomtown", "business_name"=>"Cosmic Bloom Emporium", "tin"=>"5678901234", "contact_number"=>"555-1234", "email"=>"cosmicbloom@example.com", "seller_type"=>"CORP"],
                ["name"=>"Golden Tranquility Bazaar", "region_code"=>"08", "type"=>"product", "vat_type"=>"V", "registered_name"=>"Golden Tranquility Bazaar", "registered_address"=>"888 Tranquil Street, Goldentown", "business_name"=>"Golden Tranquility Bazaar", "tin"=>"6789012345", "contact_number"=>"555-2345", "email"=>"goldentranquility@example.com", "seller_type"=>"INDIVIDUAL"],
                ["name"=>"Sapphire Serenity Haven", "region_code"=>"09", "type"=>"service", "vat_type"=>"NV", "registered_name"=>"Sapphire Serenity Haven", "registered_address"=>"999 Sapphire Lane, Serenestown", "business_name"=>"Sapphire Serenity Haven", "tin"=>"7890123456", "contact_number"=>"555-3456", "email"=>"sapphireserenity@example.com", "seller_type"=>"CORP"],
                ["name"=>"Urban Elegance Boutique", "region_code"=>"10", "type"=>"product", "vat_type"=>"V", "registered_name"=>"Urban Elegance Boutique", "registered_address"=>"1010 Stylish Street, Urbantown", "business_name"=>"Urban Elegance Boutique", "tin"=>"8901234567", "contact_number"=>"555-4567", "email"=>"eleganturban@example.com", "seller_type"=>"INDIVIDUAL"]
            ];
        }
        $loopLength = $this->option('loop_length');
        $totalDays = $this->option('total_days');
        $subDays =  $this->option('sub_days');
        $sellerCount =  $this->option('seller_count');
        $providerCount =  $this->option('provider_count');
        $currentMoment = Carbon::now()->subDays($totalDays);
        $endMoment = Carbon::now()->subDays($subDays);

        while ($currentMoment->lessThan($endMoment)) {
            echo "Loop at " . $currentMoment->format('Y-m-d') . PHP_EOL;
            $cdate = $currentMoment->format('Y-m-d');

            // Take the first 10 items from the shuffled array
            $sellers = collect($stores)->shuffle()->take($sellerCount)->toArray();
            $dsps  = ServiceProvider::with(["fees"=> function($q){
                $q->where("status","ACTIVE");
            }])
            ->whereHas('fees', function ($query) {
                $query->where('status','=',"ACTIVE");            
            })
            ->where("status","ACTIVE")
            ->inRandomOrder()
            ->limit($providerCount)
            ->get();

            // echo json_encode($sellers);
            foreach ($sellers as $seller) {
                echo json_encode($seller);
                foreach($dsps as $dsp)
                {
                    for ($i = 0; $i <= $loopLength; $i++) {
                        $transId = "GEN" . strtoupper(substr(base_convert(rand(), 10, 36), 2, 15));
                        $elStatus = $status[array_rand($status)];
                        $shippingFee = mt_rand(45, 90);
                        $voucher = mt_rand(0, 10);
                        $coins = mt_rand(0, 5);
                        $minAmount = 200;
                        $maxAmount = 500;
                        if ($stores_type!="NON"){
                            $maxAmount = 10000;
                        }
                        $subtotalAmount = rand($minAmount, $maxAmount);
                        $totalAmount = $subtotalAmount + $shippingFee - ($voucher + $coins);
                        // $seller = $stores[array_rand($stores)];
                        $seller['eligible_witheld_seller'] = $eligibleWithheldSeller[array_rand($eligibleWithheldSeller)];
                        $regionCode = $seller['region_code'];
                        $type = $seller['type'];
                        $remittedDate = (rand(0, 1) < 0.5) ? Carbon::now()->format('Y-m-d H:i:s') : null;

                        $items = [
                            [
                                "description" => "Samsung Galaxy Buds 2 Wireless Bluetooth Earphones Business Stereo Headset Noise Cancelling with Mic",
                                "variant" => "black",
                                "qty" => 1,
                                "unit_price" => $subtotalAmount,
                                "total_price" => $subtotalAmount,
                                "image" => ""
                            ],
                        ];

                        $faker = Faker::create('en_PH');
                        $customer = [
                            "first_name"=>$faker->firstName,
                            "middle_name"=>"01",
                            "last_name"=>$faker->lastName,
                            "birth_date"=>  $faker->dateTimeThisCentury->format('Y-m-d'),
                            "mobile_number"=> $faker->phoneNumber,
                            "email"=> $faker->email
                        ];

                        // echo json_encode($customer);

                        if ($type == "service") {
                            $items = [
                                [
                                    "description" => "Social Media Content \nEngaging and share-worthy social media posts that captivate your audience and drive engagement",
                                    "variant" => "",
                                    "qty" => 1,
                                    "unit_price" => $subtotalAmount,
                                    "total_price" => $subtotalAmount,
                                    "image" => ""
                                ],
                            ];
                        }

                        $transaction = (object)[
                            "trans_id" => $transId,
                            "shipping_fee" => $shippingFee,
                            "voucher" => $voucher,
                            "coins" => $coins,
                            "subtotal_amount" => $subtotalAmount,
                            "total_amount" => $totalAmount,
                            "status" => $elStatus,
                            "region_code" => $regionCode,
                            "status_date" => $currentMoment->format('Y-m-d H:i:s'), // Assuming cdate is the current date
                            "remitted_date" => $remittedDate,
                            "type" => $type,
                            "seller" => $seller,
                            "customer" => $customer,
                            "items" => $items
                        ];
                
                        $this->transact($transaction,$dsp);

                        // Use $transaction as needed in your Laravel application
                    }
                }

            }

            
            // Your additional logic here
            // Increment the current moment by one day for the next iteration
            $currentMoment->addDay();
        }
    }

    private function transact($request,$dsp)
    {
        // $dsp = ServiceProvider::with(["fees"=> function($q){
        //     $q->where("status","ACTIVE");
        // }])
        // ->whereHas('fees', function ($query) {
        //     $query->where('status','=',"ACTIVE");            
        // })
        // ->where("status","ACTIVE")
        // ->inRandomOrder()
        // ->first();

        $current_date = date('Y-m-d');
        
        $region_code = $request->region_code;
        try {
            //get transaction if exist;
            $transaction = Transaction::where("service_provider_id",$dsp->id)
            ->where("trans_id",$request->trans_id)
            ->first();

            $status = $request->status;
            
            //check if the transaction is not pending
            if ($transaction && $transaction->status!="PENDING") return;

            $items = collect($request->items)->map(function ($item) {
                $item['item'] = $item['description'];
                return $item;
            });

            //check total of items vs subtotal
            $total = collect($items)->sum("total_price");
            if ($total!=$request->subtotal_amount) return;

            $additional_fees = $request->shipping_fee;
            $discounts = $request->voucher + $request->coins;
            $net = ($total + $additional_fees) - $discounts;
            $subtotal = ($total  - $discounts);

            //check net total vs computation from client
            if ($net!=$request->total_amount) return;


            $fee_type = ["TRANSACTION","SERVICE","COMMISSION"];
            $fee_list = $dsp->fees; //array of collection

            $computed_fee = [];

            foreach ($fee_type as $type) {
                $amount = 0;
                $fee = $fee_list->where("type",$type)->where("min","<=",$net)->where("max",">=",$net)->first();
                if (!$fee)
                    $fee = $fee_list->where("type",$type)->where("min",0)->where("max",0)->first();

                if ($fee)
                {
                    if($type=="COMMISSION" && $fee->amount_type=="PERCENTAGE")
                    {
                        $percentage_fee = $fee->amount / 100;
                        $amount = $subtotal * $percentage_fee;
                    }
                    else if($fee->amount_type=="PERCENTAGE")
                    {
                        $percentage_fee = $fee->amount / 100;
                        $amount = $net * $percentage_fee;
                    }
                    else if($fee->amount_type=="FIXED")
                    {
                        $amount = $fee->amount;
                    }
                }

                array_push($computed_fee, $amount);
            }

            $online_platform_vat = (array_sum($computed_fee) / 1.12) * 0.12;
            $shipping_vat = ($request->shipping_fee / 1.12) * 0.12;
            $product_less_discount = $request->subtotal_amount - $discounts;
            if ($request->seller["vat_type"]=="NV"){
                $base_price = $product_less_discount;
                $item_vat = 0;
            }else{
                $base_price = ($product_less_discount / 1.12);
                $item_vat = $base_price * 0.12;
            }
            
            $withholding_tax = $base_price * 0.005;
            $tax = $online_platform_vat + $shipping_vat + $item_vat + $withholding_tax;

            return DB::transaction(function () 
            use($transaction, $items, $dsp, $request, $computed_fee,$online_platform_vat,$shipping_vat,$item_vat,$base_price,$withholding_tax, $tax, $status, $current_date,$region_code) {
                $new = true;
                $old_status = $status;
                if ($transaction){
                    $new = false;
                    $old_status = $transaction->status;
                }
                $customer = $this->createCustomer($dsp,$new,$transaction,$request);
                $seller = $this->createSeller($dsp,$new,$transaction,$request,$base_price,$status);

                if ($new){
                    $reference_number = Helper::ref_number($dsp->prefix."0",32);
                    $transaction = new Transaction();
                    // $transaction->or_number = Helper::ref_number($dsp->prefix."0",15);
                    $transaction->service_provider_id = $dsp->id;
                    $transaction->trans_id = $request->trans_id;
                    $transaction->reference_number = $reference_number;
                }
                $transaction->customer_id = ($customer) ? $customer->id : null;
                $transaction->seller_id = ($seller) ? $seller->id : null;
                $transaction->shipping_fee = $request->shipping_fee;
                $transaction->voucher = $request->voucher;
                $transaction->coins = $request->coins;
                $transaction->subtotal_amount = $request->subtotal_amount;
                $transaction->total_amount = $request->total_amount;
                $transaction->transaction_fee = $computed_fee[0];
                $transaction->service_fee = $computed_fee[1];
                $transaction->commission_fee = $computed_fee[2];
                $transaction->online_platform_vat = $online_platform_vat;
                $transaction->shipping_vat = $shipping_vat;
                $transaction->item_vat = $item_vat;
                $transaction->base_price = $base_price;
                $transaction->vat_type = $request->seller["vat_type"];
                $transaction->withholding_tax = $withholding_tax;
                $transaction->tax = $tax;
                $transaction->status = $status;
                $transaction->region_code = $region_code;
                $transaction->email_notified = 2;
                $transaction->type = ($request->type) ? $request->type : "PRODUCT";
                $transaction->remitted_date = $request->remitted_date  ? $request->remitted_date : null;
                $transaction[strtolower($status)."_date"] = ($request->status_date!="") ? $request->status_date : Carbon::now();
                $transaction->save();

                if ($new)
                {
                    //for setting or_number
                    DB::Statement("update
                    transactions as t inner join series as s on t.service_provider_id=s.service_provider_id
                    set t.or_number=s.complete_no,s.status=0
                    where s.status=1 and t.id=$transaction->id limit 1;");

                    TransactionSummaryController::update($new, $dsp, $transaction, strtolower($status), strtolower($status), $transaction[strtolower($status)."_date"]);
                }
                else
                {
                    TransactionSummaryController::update($new, $dsp, $transaction, strtolower($status), strtolower($old_status), $transaction[strtolower($status)."_date"]);
                }
                
                $transaction->details()->delete();
                $transaction->details()->createMany($items);

                //2023-06-14 - remove by jondee rigor
                // if($status=="COMPLETED" && isset($request->customer))
                // {
                //     if($request->customer["email"])
                //         $notify = EmailNotificationController::officialReceipt($transaction->reference_number);
                // }

                foreach ($items as $key => $value) {
                    $image=isset($value["image"]) ? $value["image"] : "";
                    $item = $transaction->details[$key];
                    if($image){
                        FileController::fileUpload($item->id,$item->id,"transaction_details",$image);
                    }
                }

                $data = [
                    "reference_number"=>$transaction->reference_number,
                    "transaction_fee"=>limitDecimal($transaction->transaction_fee,2),
                    "service_fee"=>limitDecimal($transaction->service_fee,2),
                    "commission_fee"=>limitDecimal($transaction->commission_fee,2),
                    "online_platform_vat"=>limitDecimal($transaction->online_platform_vat,2),
                    "shipping_vat"=>limitDecimal($transaction->shipping_vat,2),
                    "item_vat"=>limitDecimal($transaction->item_vat,2),
                    "base_price"=>limitDecimal($transaction->base_price,2),
                    "withholding_tax"=>limitDecimal($transaction->withholding_tax,2),
                    "tax"=>limitDecimal($transaction->tax,2)
                ];

                $test = Helper::useRedis("new_transaction",$data);

                return response()->json(["status"=>1,"data"=>$data], 200)->setEncodingOptions(JSON_NUMERIC_CHECK);
            },20);
        
        } catch (Throwable $e) {
            throw $e;
            return false;
        }
    }

    private function createCustomer($dsp,$new,$transaction,$request){
        $customer = null;
        if (isset($request->customer))
        {
            if ($new){
                $customer = Customer::firstOrNew(
                    [
                        'service_provider_id' => $dsp->id,
                        'first_name' => isset($request->customer["first_name"]) ? $request->customer["first_name"] : "",
                        'middle_name' => isset($request->customer["middle_name"]) ? $request->customer["middle_name"] : "",
                        'last_name' => isset($request->customer["last_name"]) ? $request->customer["last_name"] : "",
                        'birth_date' => isset($request->customer["birth_date"]) ? $request->customer["birth_date"] : null
                    ]
                );
            }
            else
            {
                $customer = Customer::firstOrNew(
                    [
                        'id' => $transaction->customer_id
                    ]
                );
                $customer->first_name = isset($request->customer["first_name"]) ? $request->customer["first_name"] : "";
                $customer->middle_name = isset($request->customer["middle_name"]) ? $request->customer["middle_name"] : "";
                $customer->last_name = isset($request->customer["last_name"]) ? $request->customer["last_name"] : "";
                $customer->birth_date = isset($request->customer["birth_date"]) ? $request->customer["birth_date"] : null;
            }
            $customer->mobile_number = isset($request->customer["mobile_number"]) ? $request->customer["mobile_number"] : "";
            $customer->email = isset($request->customer["email"]) ? $request->customer["email"] : "";
            $customer->save();
        }

        return $customer;
    }

    private function createSeller($dsp,$new,$transaction,$request,$base_price,$status){
        $seller = null;
        $region_code = ($request->region_code) ? $request->region_code : "01";
        if (isset($request->seller))
        {
            try
            {
            
                if ($new){
                    $seller = Seller::firstOrNew(
                        [
                            'registered_name' => isset($request->seller["registered_name"]) ? $request->seller["registered_name"] : "",
                            'tin' => $request->seller["tin"],
                            "region_code"=>$region_code
                        ]
                    );
                }
                else
                {
                    
                    $seller = Seller::firstOrNew(
                        [
                            'id' => $transaction->seller_id
                        ]
                    );
                }

                $seller->registered_address = isset($request->seller["registered_address"]) ? $request->seller["registered_address"] : "";
                $seller->business_name = isset($request->seller["business_name"]) ? $request->seller["business_name"] : "";
                // $seller->tin = isset($request->seller["tin"]) ? $request->seller["tin"] : "";
                $seller->email = isset($request->seller["email"]) ? $request->seller["email"] : "";
                $seller->vat_type = $request->seller["vat_type"];
                $seller->type = $request->seller["seller_type"] ?? "INDIVIDUAL";
                // $seller->eligible_witheld_seller = $request->seller["eligible_witheld_seller"];
                $seller->contact_number = $request->seller["contact_number"];
                $seller->save();

            } catch (\Exception $e) {
                $seller = Seller::where("tin",$request->seller["tin"])->lockForUpdate()->first();
                if (!$seller){
                    $seller = New Seller();
                    $seller->tin = $request->seller["tin"];
                }
                $seller->registered_name = isset($request->seller["registered_name"]) ? $request->seller["registered_name"] : "";
                $seller->registered_address = isset($request->seller["registered_address"]) ? $request->seller["registered_address"] : "";
                $seller->business_name = isset($request->seller["business_name"]) ? $request->seller["business_name"] : "";
                // $seller->tin = isset($request->seller["tin"]) ? $request->seller["tin"] : "";
                $seller->email = isset($request->seller["email"]) ? $request->seller["email"] : "";
                $seller->vat_type = $request->seller["vat_type"];
                $seller->type = $request->seller["seller_type"] ?? "INDIVIDUAL";
                // $seller->eligible_witheld_seller = $request->seller["eligible_witheld_seller"];
                $seller->contact_number = $request->seller["contact_number"];
                $seller->save();
            }

            //for demo only -2024-18-01 -jondee
            if ($status=="COMPLETED")
            {
                if($seller->tin!="029203920132")
                {
                    $cor = rand(0,1) == 1;
                    $seller->update(["sales_per_anum"=>DB::raw("sales_per_anum+".$base_price),"has_cor"=>$cor]);
                }
                else
                {
                    $seller->update(["sales_per_anum"=>DB::raw("sales_per_anum+".$base_price)]);
                }
               
                $seller = $seller->fresh();
    
                if(floatval($seller->sales_per_anum)>=500000 && $seller->eligible_witheld_seller=="NONE"){
                    if($seller->tin!="029203920132")
                    {
                        $status = collect(['ELIGIBLE', 'ACTIVE'])->random();
                        if ($status=="ACTIVE") $seller->update(["eligible_witheld_seller"=>$status,"has_cor"=>1]);
                        else $seller->update(["eligible_witheld_seller"=>$status]);
                        
                    }
                    else
                    {
                        $seller->update(["eligible_witheld_seller"=>"ELIGIBLE"]);
                    }
                
                }
            }
            //for demo only -2024-18-01 -jondee

            $user = SellerServiceProvider::firstOrCreate(
                [
                    'seller_id' =>  $seller->id,
                    'service_provider_id' => $dsp->id
                ]
            );
        }

        return $seller;
    }
}
