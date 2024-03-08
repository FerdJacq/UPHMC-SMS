const axios = require('axios');
const moment = require("moment");

let url = "https://taxengine.psmed.org/api/transaction";
// let url = "http://127.0.0.1:8000/api/transaction";
let status = ["PENDING","ONGOING","COMPLETED"]
let types = ["PRODUCT","SERVICE"];
let region_codes = ["01","02","03","04","05","06","07","08","09","10","11","12","13","14"];
let eligible_witheld_seller = ["NONE", "ELIGIBLE", "ACTIVE"];
let dsps = [
    {"dsp-name":"SHOPEE","dsp-code":"DSPNWLS","dsp-token":"HGX5LRxgkzkBRixt","dsp-secret":"cJ1RJnoPIsavMiko3N5hQ7ky"},
    {"dsp":"GRAB","dsp-code":"DSPNWLT","dsp-token":"HGX5LRxgkzkBRixt","dsp-secret":"cJ1RJnoPIsavMiko3N5hQ7ky"},
    {"dsp":"SHOPSM","dsp-code":"DSPNWLU","dsp-token":"HGX5LRxgkzkBRixt","dsp-secret":"cJ1RJnoPIsavMiko3N5hQ7ky"},
    {"dsp":"TIKTOK","dsp-code":"DSPNWLV","dsp-token":"HGX5LRxgkzkBRixt","dsp-secret":"cJ1RJnoPIsavMiko3N5hQ7ky"},
    {"dsp":"BRITEPH","dsp-code":"DSPNWLW","dsp-token":"HGX5LRxgkzkBRixt","dsp-secret":"cJ1RJnoPIsavMiko3N5hQ7ky"},
];
let sellers = [
    {
      "type": "CORP",
      "registered_name": "Seller 1",
      "registered_address": "Address 1",
      "business_name": "Business 1",
      "tin": "029203920192",
      "contact_number": "",
      "email": "email1@example.com",
      "vat_type":"V"
    },
    {
      "type": "CORP",
      "registered_name": "Seller 2",
      "registered_address": "Address 2",
      "business_name": "Business 2",
      "tin": "029203920193",
      "contact_number": "",
      "email": "email2@example.com",
      "vat_type":"V"
    },
    {
      "type": "INDIVIDUAL",
      "registered_name": "Seller 3",
      "registered_address": "Address 3",
      "business_name": "Business 3",
      "tin": "029203920194",
      "contact_number": "",
      "email": "email3@example.com",
      "vat_type":"NV"
    },
    {
      "type": "CORP",
      "registered_name": "Seller 4",
      "registered_address": "Address 4",
      "business_name": "Business 4",
      "tin": "029203920195",
      "contact_number": "",
      "email": "email4@example.com",
      "vat_type":"NV"
    },
    {
      "type": "INDIVIDUAL",
      "registered_name": "Seller 5",
      "registered_address": "Address 5",
      "business_name": "Business 5",
      "tin": "029203920196",
      "contact_number": "",
      "email": "email5@example.com",
      "vat_type":"NV"
    },
    {
      "type": "INDIVIDUAL",
      "registered_name": "Seller 6",
      "registered_address": "Address 6",
      "business_name": "Business 6",
      "tin": "029203920197",
      "contact_number": "",
      "email": "email6@example.com",
      "vat_type":"V"
    }
];

let stores = [
  {"name": "Shop Smart", "region_code": "", "type": "product", "vat_type": "NV", "registered_name": "Smart Retail Inc.", "registered_address": "123 Main St, Cityville", "business_name": "Shop Smart", "tin": "123456789", "contact_number": "555-1234", "email": "shopsmart@example.com", "seller_type":"INDIVIDUAL"},
  {"name": "Fashion Haven", "region_code": "01", "type": "product", "vat_type": "V", "registered_name": "Fashion Haven Corp.", "registered_address": "456 Fashion Blvd, Style City", "business_name": "Fashion Haven", "tin": "987654321", "contact_number": "555-5678", "email": "fashionhaven@example.com", "seller_type":"INDIVIDUAL"},
  {"name": "Gourmet Delights", "region_code": "02", "type": "product", "vat_type": "NV", "registered_name": "Gourmet Delights Ltd.", "registered_address": "789 Gourmet Ave, Foodtown", "business_name": "Gourmet Delights", "tin": "654321987", "contact_number": "555-4321", "email": "gourmetdelights@example.com", "seller_type":"INDIVIDUAL"},
  {"name": "Tech Trends", "region_code": "03", "type": "product", "vat_type": "V", "registered_name": "Tech Trends Solutions", "registered_address": "101 Innovation St, Techville", "business_name": "Tech Trends", "tin": "321987654", "contact_number": "555-8765", "email": "techtrends@example.com", "seller_type":"INDIVIDUAL"},
  {"name": "Home Harmony", "region_code": "04", "type": "product", "vat_type": "NV", "registered_name": "Home Harmony Homes Inc.", "registered_address": "456 Comfort Lane, Homestead", "business_name": "Home Harmony", "tin": "789654321", "contact_number": "555-9876", "email": "homeharmony@example.com", "seller_type":"INDIVIDUAL"},
  {"name": "Nature's Nook", "region_code": "05", "type": "product", "vat_type": "V", "registered_name": "Nature's Nook Nature Co.", "registered_address": "789 Greenery Rd, Nature City", "business_name": "Nature's Nook", "tin": "456789123", "contact_number": "555-2345", "email": "naturesnook@example.com", "seller_type":"INDIVIDUAL"},
  {"name": "Pet Paradise", "region_code": "06", "type": "product", "vat_type": "NV", "registered_name": "Pet Paradise LLC", "registered_address": "555 Pet Street, Animaland", "business_name": "Pet Paradise", "tin": "789123456", "contact_number": "555-8765", "email": "petparadise@example.com", "seller_type":"INDIVIDUAL"},
  {"name": "Books & Beyond", "region_code": "07", "type": "product", "vat_type": "V", "registered_name": "Literary Ventures Inc.", "registered_address": "777 Book Lane, Readville", "business_name": "Books & Beyond", "tin": "987654789", "contact_number": "555-4321", "email": "booksandbeyond@example.com", "seller_type":"INDIVIDUAL"},
  {"name": "Cosmic Crafts", "region_code": "08", "type": "product", "vat_type": "NV", "registered_name": "Cosmic Creations Ltd.", "registered_address": "888 Space Blvd, Galaxy City", "business_name": "Cosmic Crafts", "tin": "654789321", "contact_number": "555-2345", "email": "cosmiccrafts@example.com", "seller_type":"INDIVIDUAL"},
  {"name": "Vintage Vogue", "region_code": "09", "type": "product", "vat_type": "V", "registered_name": "Vintage Vogue Inc.", "registered_address": "999 Fashion Street, Retroville", "business_name": "Vintage Vogue", "tin": "321987654", "contact_number": "555-8765", "email": "vintagevogue@example.com", "seller_type":"INDIVIDUAL"},
  {"name": "Wellness Wonders", "region_code": "10", "type": "product", "vat_type": "NV", "registered_name": "Wellness World Ltd.", "registered_address": "101 Health Haven, Welltown", "business_name": "Wellness Wonders", "tin": "789654321", "contact_number": "555-1234", "email": "wellnesswonders@example.com", "seller_type":"INDIVIDUAL"},
  {"name": "Sports Spectrum", "region_code": "11", "type": "product", "vat_type": "V", "registered_name": "Sports Spectrum Inc.", "registered_address": "222 Sports Ave, Sportsville", "business_name": "Sports Spectrum", "tin": "123456789", "contact_number": "555-5678", "email": "sportsspectrum@example.com", "seller_type":"INDIVIDUAL"},
  {"name": "Electro Elegance", "region_code": "12", "type": "product", "vat_type": "NV", "registered_name": "Electric Elegance Solutions", "registered_address": "333 Tech Street, Electrotown", "business_name": "Electro Elegance", "tin": "987654321", "contact_number": "555-8765", "email": "electroelegance@example.com", "seller_type":"INDIVIDUAL"},
  {"name": "Toy Time", "region_code": "13", "type": "product", "vat_type": "V", "registered_name": "Toy Time Toys Ltd.", "registered_address": "444 Toy Lane, Playville", "business_name": "Toy Time", "tin": "321987654", "contact_number": "555-2345", "email": "toytime@example.com", "seller_type":"INDIVIDUAL"},
  {"name": "Jewel Junction", "region_code": "14", "type": "product", "vat_type": "NV", "registered_name": "Gem Treasures LLC", "registered_address": "567 Gemstone Lane, Precious City", "business_name": "Jewel Junction", "tin": "654789321", "contact_number": "555-7890", "email": "jeweljunction@example.com", "seller_type":"INDIVIDUAL"},
  {"name": "Culinary Canvas", "region_code": "", "type": "service", "vat_type": "V", "registered_name": "Taste Palette Catering", "registered_address": "789 Culinary St, Flavor Town", "business_name": "Culinary Canvas", "tin": "987654789", "contact_number": "555-0987", "email": "culinarycanvas@example.com", "seller_type":"INDIVIDUAL"},
  {"name": "Artistic Attire", "region_code": "01", "type": "product", "vat_type": "NV", "registered_name": "Creative Couture Inc.", "registered_address": "101 Artistic Blvd, Design City", "business_name": "Artistic Attire", "tin": "456123987", "contact_number": "555-8765", "email": "artisticattire@example.com", "seller_type":"INDIVIDUAL"},
  {"name": "Tool Time", "region_code": "02", "type": "product", "vat_type": "V", "registered_name": "Handy Hardware Solutions", "registered_address": "222 Tool Lane, Toolbox", "business_name": "Tool Time", "tin": "321789456", "contact_number": "555-2345", "email": "tooltime@example.com", "seller_type":"INDIVIDUAL"},
  {"name": "Organic Oasis", "region_code": "03", "type": "product", "vat_type": "NV", "registered_name": "Organic Harvest Ltd.", "registered_address": "333 Green Grove, Pureland", "business_name": "Organic Oasis", "tin": "123987456", "contact_number": "555-5678", "email": "organicoasis@example.com", "seller_type":"INDIVIDUAL"},
  {"name": "Music Magic", "region_code": "04", "type": "product", "vat_type": "V", "registered_name": "Melody Masters Inc.", "registered_address": "444 Harmony Street, Musictown", "business_name": "Music Magic", "tin": "789321654", "contact_number": "555-9876", "email": "musicmagic@example.com", "seller_type":"INDIVIDUAL"},
  {"name": "Essential Emporium", "region_code": "05", "type": "product", "vat_type": "NV", "registered_name": "Essential Elegance Corp.", "registered_address": "555 Essential Ave, Necessity City", "business_name": "Essential Emporium", "tin": "987456321", "contact_number": "555-2345", "email": "essentialemporium@example.com", "seller_type":"INDIVIDUAL"},
  {"name": "Fitness Fusion", "region_code": "06", "type": "service", "vat_type": "V", "registered_name": "FitForm Solutions LLC", "registered_address": "666 Health Lane, Fitville", "business_name": "Fitness Fusion", "tin": "321654987", "contact_number": "555-8765", "email": "fitnessfusion@example.com", "seller_type":"INDIVIDUAL"},
  {"name": "Marvelous Munchies", "region_code": "07", "type": "product", "vat_type": "NV", "registered_name": "Munchie Marvels Inc.", "registered_address": "777 Tasty Blvd, Snacksville", "business_name": "Marvelous Munchies", "tin": "654789012", "contact_number": "555-7654", "email": "munchiemarvels@example.com", "seller_type":"INDIVIDUAL"},
  {"name": "Urban Umbrella", "region_code": "08", "type": "product", "vat_type": "V", "registered_name": "City Shelter Co.", "registered_address": "888 Rainy Street, Umbrellatown", "business_name": "Urban Umbrella", "tin": "987012345", "contact_number": "555-8901", "email": "urbanumbrella@example.com", "seller_type":"INDIVIDUAL"},
  {"name": "Global Groove", "region_code": "09", "type": "product", "vat_type": "NV", "registered_name": "Global Groove Enterprises", "registered_address": "999 Groovy Ave, Worldbeat City", "business_name": "Global Groove", "tin": "012345678", "contact_number": "555-6789", "email": "globalgroove@example.com", "seller_type":"INDIVIDUAL"},
  {"name": "Silver Lining", "region_code": "10", "type": "service", "vat_type": "V", "registered_name": "Silver Lining Solutions", "registered_address": "101 Cloud Street, Positivetown", "business_name": "Silver Lining", "tin": "876543210", "contact_number": "555-0123", "email": "silverlining@example.com", "seller_type":"INDIVIDUAL"},
  {"name": "Sweet Symphony", "region_code": "11", "type": "service", "vat_type": "NV", "registered_name": "Symphony Sweets Co.", "registered_address": "222 Harmony Lane, Melodyville", "business_name": "Sweet Symphony", "tin": "109876543", "contact_number": "555-3456", "email": "sweetsymphony@example.com", "seller_type":"INDIVIDUAL"},
  {"name": "Safari Styles", "region_code": "12", "type": "product", "vat_type": "V", "registered_name": "Safari Styles Ltd.", "registered_address": "333 Wilderness Ave, Adventuretown", "business_name": "Safari Styles", "tin": "432109876", "contact_number": "555-6789", "email": "safaristyles@example.com", "seller_type":"INDIVIDUAL"},
  {"name": "Luxury Lane", "region_code": "13", "type": "product", "vat_type": "NV", "registered_name": "Luxury Lane Enterprises", "registered_address": "444 Opulence Street, Richville", "business_name": "Luxury Lane", "tin": "567890123", "contact_number": "555-0123", "email": "luxurylane@example.com", "seller_type":"INDIVIDUAL"},
  {"name": "Outdoor Oasis", "region_code": "14", "type": "product", "vat_type": "V", "registered_name": "Outdoor Oasis Inc.", "registered_address": "567 Nature Lane, Outdoorsville", "business_name": "Outdoor Oasis", "tin": "901234567", "contact_number": "555-3456", "email": "outdooroasis@example.com", "seller_type":"INDIVIDUAL"},
  {"name": "Eco Elegance", "region_code": "", "type": "service", "vat_type": "NV", "registered_name": "Eco Elegance Solutions", "registered_address": "789 Green Street, Ecotown", "business_name": "Eco Elegance", "tin": "890123456", "contact_number": "555-6789", "email": "ecoelegance@example.com", "seller_type":"INDIVIDUAL"},
  {"name": "Floral Fantasy", "region_code": "01", "type": "product", "vat_type": "V", "registered_name": "Blossom Blooms Co.", "registered_address": "101 Flower Blvd, Bloomington", "business_name": "Floral Fantasy", "tin": "567890123", "contact_number": "555-0123", "email": "floralfantasy@example.com", "seller_type":"INDIVIDUAL"},
  {"name": "Digital Den", "region_code": "02", "type": "product", "vat_type": "NV", "registered_name": "Digital Den Solutions", "registered_address": "222 Tech Street, Digitown", "business_name": "Digital Den", "tin": "345678901", "contact_number": "555-7890", "email": "digitalden@example.com", "seller_type":"INDIVIDUAL"},
  {"name": "Crafty Corner", "region_code": "03", "type": "service", "vat_type": "V", "registered_name": "Crafty Creations Co.", "registered_address": "333 Artistic Ave, Craftsville", "business_name": "Crafty Corner", "tin": "456789012", "contact_number": "555-0123", "email": "craftycorner@example.com", "seller_type":"INDIVIDUAL"},
  {"name": "Sunny Seeds", "region_code": "04", "type": "product", "vat_type": "NV", "registered_name": "Sunny Seeds Farms Ltd.", "registered_address": "444 Sunshine Blvd, Seedstown", "business_name": "Sunny Seeds", "tin": "567890123", "contact_number": "555-3456", "email": "sunnyseeds@example.com", "seller_type":"INDIVIDUAL"},
  {"name": "Sculpted Spaces", "region_code": "05", "type": "service", "vat_type": "V", "registered_name": "Sculpted Spaces Interiors", "registered_address": "555 Art Street, Sculptureville", "business_name": "Sculpted Spaces", "tin": "678901234", "contact_number": "555-6789", "email": "sculptedspaces@example.com", "seller_type":"INDIVIDUAL"},
  {"name": "Innovative Interiors", "region_code": "06", "type": "product", "vat_type": "NV", "registered_name": "Innovative Interiors Ltd.", "registered_address": "666 Design Lane, Innovation City", "business_name": "Innovative Interiors", "tin": "789012345", "contact_number": "555-0123", "email": "innovativeinteriors@example.com", "seller_type":"INDIVIDUAL"},
  {"name": "Joyful Journeys", "region_code": "07", "type": "product", "vat_type": "V", "registered_name": "Joyful Journeys Travel Co.", "registered_address": "777 Adventure Ave, Traveltown", "business_name": "Joyful Journeys", "tin": "890123456", "contact_number": "555-3456", "email": "joyfuljourneys@example.com", "seller_type":"INDIVIDUAL"},
  {"name": "Clever Creations", "region_code": "08", "type": "product", "vat_type": "NV", "registered_name": "Clever Creations Ltd.", "registered_address": "888 Innovation St, Cleverville", "business_name": "Clever Creations", "tin": "901234567", "contact_number": "555-6789", "email": "clevercreations@example.com", "seller_type":"INDIVIDUAL"},
  {"name": "Blissful Bites", "region_code": "09", "type": "product", "vat_type": "V", "registered_name": "Blissful Bites Catering", "registered_address": "999 Culinary Blvd, Blissville", "business_name": "Blissful Bites", "tin": "012345678", "contact_number": "555-0123", "email": "blissfulbites@example.com", "seller_type":"INDIVIDUAL"},
  {"name": "Wondrous Waves", "region_code": "10", "type": "product", "vat_type": "NV", "registered_name": "Wondrous Waves Surf Co.", "registered_address": "101 Surf Lane, Wavetown", "business_name": "Wondrous Waves", "tin": "123456789", "contact_number": "555-3456", "email": "wondrouswaves@example.com", "seller_type":"INDIVIDUAL"},
  {"name": "Heritage Haven", "region_code": "11", "type": "service", "vat_type": "V", "registered_name": "Heritage Haven Tours", "registered_address": "222 Heritage Street, Heritagetown", "business_name": "Heritage Haven", "tin": "234567890", "contact_number": "555-6789", "email": "heritagehaven@example.com", "seller_type":"INDIVIDUAL"},
  {"name": "Aromatic Ambiance", "region_code": "12", "type": "product", "vat_type": "NV", "registered_name": "Aromatic Ambiance Inc.", "registered_address": "333 Fragrance Blvd, Scentville", "business_name": "Aromatic Ambiance", "tin": "345678901", "contact_number": "555-0123", "email": "aromaticambiance@example.com", "seller_type":"INDIVIDUAL"},
  {"name": "Golden Groves", "region_code": "13", "type": "product", "vat_type": "V", "registered_name": "Golden Groves Farms Ltd.", "registered_address": "444 Orchard Lane, Goldentown", "business_name": "Golden Groves", "tin": "456789012", "contact_number": "555-3456", "email": "goldengroves@example.com", "seller_type":"INDIVIDUAL"},
    {"name": "Chic Charms", "region_code": "14", "type": "product", "vat_type": "NV", "registered_name": "Chic Charms Jewelry Co.", "registered_address": "555 Elegance Ave, Styleville", "business_name": "Chic Charms", "tin": "567890123", "contact_number": "555-6789", "email": "chiccharms@example.com", "seller_type":"INDIVIDUAL"},
    {"name": "Celestial Ceramics", "region_code": "", "type": "service", "vat_type": "V", "registered_name": "Celestial Ceramics Studio", "registered_address": "789 Arts Street, Celestial City", "business_name": "Celestial Ceramics", "tin": "678901234", "contact_number": "555-0123", "email": "celestialceramics@example.com", "seller_type":"CORP"},
    {"name": "Adventure Awaits", "region_code": "01", "type": "service", "vat_type": "NV", "registered_name": "Adventure Awaits Travel Co.", "registered_address": "101 Explorer Ave, Adventurertown", "business_name": "Adventure Awaits", "tin": "789012345", "contact_number": "555-6789", "email": "adventureawaits@example.com", "seller_type":"CORP"},
    {"name": "Dazzling Designs", "region_code": "02", "type": "product", "vat_type": "V", "registered_name": "Dazzling Designs Ltd.", "registered_address": "222 Design Lane, Glitter City", "business_name": "Dazzling Designs", "tin": "890123456", "contact_number": "555-0123", "email": "dazzlingdesigns@example.com", "seller_type":"CORP"},
    {"name": "Bountiful Blessings", "region_code": "03", "type": "product", "vat_type": "NV", "registered_name": "Bountiful Blessings Farms Ltd.", "registered_address": "333 Blessings Blvd, Gratitudeville", "business_name": "Bountiful Blessings", "tin": "901234567", "contact_number": "555-3456", "email": "bountifulblessings@example.com", "seller_type":"CORP"},
    {"name": "Dynamic Dreams", "region_code": "04", "type": "product", "vat_type": "V", "registered_name": "Dynamic Dreams Inc.", "registered_address": "444 Dream Street, Dreamville", "business_name": "Dynamic Dreams", "tin": "012345678", "contact_number": "555-6789", "email": "dynamicdreams@example.com", "seller_type":"CORP"},
    {"name": "Pampered Paws", "region_code": "05", "type": "product", "vat_type": "NV", "registered_name": "Pampered Paws Pet Care", "registered_address": "555 Pet Lane, Petville", "business_name": "Pampered Paws", "tin": "123456789", "contact_number": "555-0123", "email": "pamperedpaws@example.com", "seller_type":"CORP"},
    {"name": "Zen Zephyr", "region_code": "06", "type": "product", "vat_type": "V", "registered_name": "Zen Zephyr Wellness", "registered_address": "666 Serenity St, Zen City", "business_name": "Zen Zephyr", "tin": "234567890", "contact_number": "555-3456", "email": "zenzephyr@example.com", "seller_type":"CORP"},
    {"name": "Epicurean Essence", "region_code": "07", "type": "product", "vat_type": "NV", "registered_name": "Epicurean Essence Co.", "registered_address": "777 Gourmet Lane, Epicureatown", "business_name": "Epicurean Essence", "tin": "345678901", "contact_number": "555-6789", "email": "epicureanessence@example.com", "seller_type":"CORP"},
    {"name": "Serene Senses", "region_code": "08", "type": "product", "vat_type": "V", "registered_name": "Serene Senses Spa", "registered_address": "888 Tranquil Blvd, Serenityville", "business_name": "Serene Senses", "tin": "456789012", "contact_number": "555-0123", "email": "serenesenses@example.com", "seller_type":"CORP"},
    {"name": "Rustic Radiance", "region_code": "09", "type": "product", "vat_type": "NV", "registered_name": "Rustic Radiance Crafts", "registered_address": "999 Rustic Ave, Rusticville", "business_name": "Rustic Radiance", "tin": "567890123", "contact_number": "555-3456", "email": "rusticradiance@example.com", "seller_type":"CORP"},
    {"name": "Allure Alchemy", "region_code": "10", "type": "product", "vat_type": "V", "registered_name": "Allure Alchemy Creations", "registered_address": "101 Allure Lane, Enchantment City", "business_name": "Allure Alchemy", "tin": "678901234", "contact_number": "555-6789", "email": "allurealchemy@example.com", "seller_type":"CORP"},
    {"name": "Miracle Makers", "region_code": "11", "type": "service", "vat_type": "NV", "registered_name": "Miracle Makers Foundation", "registered_address": "222 Miracle Blvd, Miracleville", "business_name": "Miracle Makers", "tin": "789012345", "contact_number": "555-0123", "email": "miraclemakers@example.com", "seller_type":"CORP"},
    {"name": "Vibrant Ventures", "region_code": "12", "type": "product", "vat_type": "V", "registered_name": "Vibrant Ventures Inc.", "registered_address": "333 Vibrant Street, Colorful City", "business_name": "Vibrant Ventures", "tin": "890123456", "contact_number": "555-3456", "email": "vibrantventures@example.com", "seller_type":"CORP"},
    {"name": "Silk & Spice", "region_code": "13", "type": "product", "vat_type": "NV", "registered_name": "Silk & Spice Textiles", "registered_address": "444 Silk Lane, Spicetown", "business_name": "Silk & Spice", "tin": "901234567", "contact_number": "555-6789", "email": "silkandspice@example.com", "seller_type":"CORP"},
    {"name": "Prismatic Pleasures", "region_code": "14", "type": "product", "vat_type": "V", "registered_name": "Prismatic Pleasures Co.", "registered_address": "555 Prism Ave, Colorville", "business_name": "Prismatic Pleasures", "tin": "012345678", "contact_number": "555-0123", "email": "prismaticpleasures@example.com", "seller_type":"CORP"},
    {"name": "Quirky Quarters", "region_code": "", "type": "service", "vat_type": "NV", "registered_name": "Quirky Quarters Design Studio", "registered_address": "789 Quirky Street, Quirktown", "business_name": "Quirky Quarters", "tin": "234567890", "contact_number": "555-3456", "email": "quirkyquarters@example.com", "seller_type":"CORP"},
    {"name": "Elite Elegance", "region_code": "01", "type": "product", "vat_type": "V", "registered_name": "Elite Elegance Couture", "registered_address": "101 Elegance Blvd, Elitetown", "business_name": "Elite Elegance", "tin": "345678901", "contact_number": "555-6789", "email": "eliteelegance@example.com", "seller_type":"CORP"},
    {"name": "Glowing Gardens", "region_code": "02", "type": "product", "vat_type": "NV", "registered_name": "Glowing Gardens Landscapes", "registered_address": "222 Garden Street, Glowville", "business_name": "Glowing Gardens", "tin": "456789012", "contact_number": "555-0123", "email": "glowinggardens@example.com", "seller_type":"CORP"},
    {"name": "Savvy Sips", "region_code": "03", "type": "service", "vat_type": "V", "registered_name": "Savvy Sips Beverages", "registered_address": "333 Savvy Lane, Sipstown", "business_name": "Savvy Sips", "tin": "567890123", "contact_number": "555-3456", "email": "savvysips@example.com", "seller_type":"CORP"},
    {"name": "Ambrosial Attic", "region_code": "04", "type": "product", "vat_type": "NV", "registered_name": "Ambrosial Attic Co.", "registered_address": "444 Ambrosia Ave, Foodville", "business_name": "Ambrosial Attic", "tin": "678901234", "contact_number": "555-6789", "email": "ambrosialattic@example.com", "seller_type":"CORP"},
    {"name": "Harmonic Hues", "region_code": "05", "type": "product", "vat_type": "V", "registered_name": "Harmonic Hues Inc.", "registered_address": "555 Palette Lane, Artville", "business_name": "Harmonic Hues", "tin": "789012345", "contact_number": "555-0123", "email": "harmonichues@example.com", "seller_type":"CORP"},
    {"name": "Plush Paws", "region_code": "06", "type": "product", "vat_type": "NV", "registered_name": "Plush Paws Pet Boutique", "registered_address": "666 Pet Street, Plushville", "business_name": "Plush Paws", "tin": "890123456", "contact_number": "555-3456", "email": "plushpaws@example.com", "seller_type":"CORP"},
    {"name": "Funky Fables", "region_code": "07", "type": "service", "vat_type": "V", "registered_name": "Funky Fables Bookstore", "registered_address": "777 Story Blvd, Talestown", "business_name": "Funky Fables", "tin": "901234567", "contact_number": "555-6789", "email": "funkyfables@example.com", "seller_type":"CORP"},
    {"name": "Fiesta Fashions", "region_code": "08", "type": "product", "vat_type": "NV", "registered_name": "Fiesta Fashions Co.", "registered_address": "888 Fiesta Ave, Styletown", "business_name": "Fiesta Fashions", "tin": "012345678", "contact_number": "555-0123", "email": "fiestafashions@example.com", "seller_type":"CORP"},
    {"name": "Nautical Nectar", "region_code": "09", "type": "product", "vat_type": "V", "registered_name": "Nautical Nectar Beverages", "registered_address": "999 Nautical Lane, Marinatown", "business_name": "Nautical Nectar", "tin": "123456789", "contact_number": "555-3456", "email": "nauticalnectar@example.com", "seller_type":"CORP"},
    {"name": "Crystal Chronicles", "region_code": "10", "type": "product", "vat_type": "NV", "registered_name": "Crystal Chronicles Jewelry", "registered_address": "101 Crystal Blvd, Gemstown", "business_name": "Crystal Chronicles", "tin": "234567890", "contact_number": "555-6789", "email": "crystalchronicles@example.com", "seller_type":"CORP"},
    {"name": "Velvet Vista", "region_code": "11", "type": "service", "vat_type": "V", "registered_name": "Velvet Vista Photography", "registered_address": "222 Velvet Street, Vistatown", "business_name": "Velvet Vista", "tin": "345678901", "contact_number": "555-0123", "email": "velvetvista@example.com", "seller_type":"CORP"},
    {"name": "Ethereal Eclat", "region_code": "12", "type": "product", "vat_type": "NV", "registered_name": "Ethereal Eclat Creations", "registered_address": "333 Ethereal Ave, Dreamland", "business_name": "Ethereal Eclat", "tin": "456789012", "contact_number": "555-3456", "email": "etherealeclat@example.com", "seller_type":"CORP"},
    {"name": "Charm City", "region_code": "13", "type": "product", "vat_type": "V", "registered_name": "Charm City Accessories", "registered_address": "444 Charm Lane, Enchantville", "business_name": "Charm City", "tin": "567890123", "contact_number": "555-6789", "email": "charmcity@example.com", "seller_type":"CORP"},
    {"name": "Emerald Echo", "region_code": "14", "type": "product", "vat_type": "NV", "registered_name": "Emerald Echo Jewelry Co.", "registered_address": "555 Gem Ave, Jewelstown", "business_name": "Emerald Echo", "tin": "678901234", "contact_number": "555-0123", "email": "emeraldecho@example.com", "seller_type":"CORP"},
    {"name": "Trendy Trinkets", "region_code": "", "type": "service", "vat_type": "V", "registered_name": "Trendy Trinkets Boutique", "registered_address": "789 Trendy Street, Chicville", "business_name": "Trendy Trinkets", "tin": "890123456", "contact_number": "555-3456", "email": "trendytrinkets@example.com", "seller_type":"CORP"},
    {"name": "Grandeur Glimpse", "region_code": "01", "type": "product", "vat_type": "NV", "registered_name": "Grandeur Glimpse Furniture", "registered_address": "101 Grand Blvd, Grandtown", "business_name": "Grandeur Glimpse", "tin": "901234567", "contact_number": "555-6789", "email": "grandeurglimpse@example.com", "seller_type":"CORP"},
    {"name": "Majestic Morsels", "region_code": "02", "type": "product", "vat_type": "NV", "registered_name": "Majestic Morsels Bakery", "registered_address": "222 Bakery Street, Delicioustown", "business_name": "Majestic Morsels", "tin": "345678901", "contact_number": "555-0123", "email": "majesticmorsels@example.com", "seller_type":"CORP"},
    {"name": "Epic Elements", "region_code": "03", "type": "service", "vat_type": "V", "registered_name": "Epic Elements Design Studio", "registered_address": "333 Element Lane, Stylish City", "business_name": "Epic Elements", "tin": "456789012", "contact_number": "555-3456", "email": "epicelements@example.com", "seller_type":"CORP"},
    {"name": "Joyous Junction", "region_code": "04", "type": "product", "vat_type": "NV", "registered_name": "Joyous Junction Toys", "registered_address": "444 Joyful Ave, Playtown", "business_name": "Joyous Junction", "tin": "567890123", "contact_number": "555-6789", "email": "joyousjunction@example.com", "seller_type":"CORP"},
    {"name": "Tropical Trends", "region_code": "05", "type": "product", "vat_type": "V", "registered_name": "Tropical Trends Clothing", "registered_address": "555 Tropical Lane, Fashionville", "business_name": "Tropical Trends", "tin": "678901234", "contact_number": "555-0123", "email": "tropicaltrends@example.com", "seller_type":"CORP"},
    {"name": "Platinum Panache", "region_code": "06", "type": "product", "vat_type": "NV", "registered_name": "Platinum Panache Jewelry", "registered_address": "666 Panache Street, Luxetown", "business_name": "Platinum Panache", "tin": "789012345", "contact_number": "555-3456", "email": "platinumpanache@example.com", "seller_type":"CORP"},
    {"name": "Cascade Couture", "region_code": "07", "type": "service", "vat_type": "V", "registered_name": "Cascade Couture Fashion House", "registered_address": "777 Couture Blvd, Elegancetown", "business_name": "Cascade Couture", "tin": "890123456", "contact_number": "555-6789", "email": "cascadecouture@example.com", "seller_type":"CORP"},
    {"name": "Moonlit Marvels", "region_code": "08", "type": "product", "vat_type": "NV", "registered_name": "Moonlit Marvels Home Decor", "registered_address": "888 Marvel Lane, Dreamville", "business_name": "Moonlit Marvels", "tin": "901234567", "contact_number": "555-0123", "email": "moonlitmarvels@example.com", "seller_type":"CORP"},
    {"name": "Spicy Splendors", "region_code": "09", "type": "product", "vat_type": "V", "registered_name": "Spicy Splendors Gourmet", "registered_address": "999 Spice Street, Flavortown", "business_name": "Spicy Splendors", "tin": "012345678", "contact_number": "555-3456", "email": "spicysplendors@example.com", "seller_type":"CORP"},
    {"name": "Tranquil Treasures", "region_code": "10", "type": "product", "vat_type": "NV", "registered_name": "Tranquil Treasures Art Gallery", "registered_address": "101 Tranquil Blvd, Artistic City", "business_name": "Tranquil Treasures", "tin": "123456789", "contact_number": "555-6789", "email": "tranquiltreasures@example.com", "seller_type":"CORP"},
    {"name": "Pristine Picks", "region_code": "11", "type": "service", "vat_type": "V", "registered_name": "Pristine Picks Antiques", "registered_address": "222 Antique Street, Collectortown", "business_name": "Pristine Picks", "tin": "234567890", "contact_number": "555-0123", "email": "pristinepicks@example.com", "seller_type":"CORP"},
    {"name": "Mystic Mingle", "region_code": "12", "type": "product", "vat_type": "NV", "registered_name": "Mystic Mingle Occult Shop", "registered_address": "333 Mystic Ave, Mystictown", "business_name": "Mystic Mingle", "tin": "345678901", "contact_number": "555-3456", "email": "mysticmingle@example.com", "seller_type":"CORP"},
    {"name": "Infinite Instincts", "region_code": "13", "type": "product", "vat_type": "V", "registered_name": "Infinite Instincts Health Store", "registered_address": "444 Infinite Lane, Wellnessville", "business_name": "Infinite Instincts", "tin": "456789012", "contact_number": "555-6789", "email": "infiniteinstincts@example.com", "seller_type":"CORP"},
    {"name": "Splendid Solace", "region_code": "14", "type": "product", "vat_type": "NV", "registered_name": "Splendid Solace Spa", "registered_address": "555 Solace Ave, Serenitytown", "business_name": "Splendid Solace", "tin": "567890123", "contact_number": "555-0123", "email": "splendidsolace@example.com", "seller_type":"CORP"},
    {"name": "Lush Labyrinth", "region_code": "", "type": "service", "vat_type": "V", "registered_name": "Lush Labyrinth Floral Studio", "registered_address": "789 Lush Street, Floratown", "business_name": "Lush Labyrinth", "tin": "678901234", "contact_number": "555-3456", "email": "lushlabyrinth@example.com", "seller_type":"CORP"},
];


let loop_length=5;
let total_days=15;
let date = "2023-12-14";

processLoop(date);
// setInterval(() => {
//     date = moment(date, 'YYYY-MM-DD').subtract(1, 'month').format('YYYY-MM-DD');
//     processLoop(date);
// }, 20000);

function processLoop(date){
    console.log(date);
    const currentMoment = moment(date, 'YYYY-MM-DD').subtract(total_days, 'days');
    const endMoment = moment(date, 'YYYY-MM-DD').add(1,'days');
    // const currentMoment = moment().subtract(total_days, 'days');
    // const endMoment = moment().add(1,'days');
    while (currentMoment.isBefore(endMoment, 'day')) {
        console.log(`Loop at ${currentMoment.format('YYYY-MM-DD')}`);
        let cdate = currentMoment.format('YYYY-MM-DD');
        for(i=0;i<=loop_length;i++){
            let trans_id = ("GEN"+Math.random().toString(36).substring(2,15)).toUpperCase();
            // let region_code = region_codes[Math.floor(Math.random()*region_codes.length)];
            let el_status = status[Math.floor(Math.random()*status.length)];
            let shipping_fee = 45;
            let voucher = 50;
            let coins = 5;
            let min_amount =200;
            let max_amount = 10000;
            let subtotal_amount =  Math.floor(Math.random() * (max_amount - min_amount + 1)) + max_amount;
            let total_amount = subtotal_amount + shipping_fee - (voucher+coins);
            let seller = stores[Math.floor(Math.random()*stores.length)];
            seller["eligible_witheld_seller"] = eligible_witheld_seller[Math.floor(Math.random()*eligible_witheld_seller.length)];
            let region_code = seller["region_code"];
            let type = seller["type"];
            let remitted_date = (Math.random() < 0.5) ?  moment(new Date()).format('YYYY-MM-DD HH:mm:ss') : null;
            
            let items = [
                {
                    "description":"Samsung Galaxy Buds 2 Wireless Bluetooth Earphones Business Stereo Headset Noise Cancelling with Mic",
                    "variant":"black",
                    "qty":1,
                    "unit_price":subtotal_amount,
                    "total_price":subtotal_amount,
                    "image":""
                },
            ]

            if (type=="service"){
            items = [
                {
                    "description":"Social Media Content \nEngaging and share-worthy social media posts that captivate your audience and drive engagement",
                    "variant":"",
                    "qty":1,
                    "unit_price":subtotal_amount,
                    "total_price":subtotal_amount,
                    "image":""
                },
            ]
            }
            
            let transaction = {
                "trans_id":trans_id,
                "shipping_fee":shipping_fee,
                "voucher":voucher,
                "coins":coins,
                "subtotal_amount":subtotal_amount,
                "total_amount":total_amount,
                "status":el_status,
                "region_code":region_code,
                "status_date":cdate,
                "remitted_date":remitted_date,
                "type":type,
                // "customer":{
                //     "first_name":"ED LUI",
                //     "middle_name":"",
                //     "last_name":"DIONCO",
                //     "mobile_number":"",
                //     "email":"edlui.diongco21@gmail.com"
                // },
                "seller":seller,
                "items":items
            }

            // let header ={
            //     "dsp-code":"",
            //     "dsp-token":"",
            //     "dsp-secret":"",
            // }
            let dsp = dsps[Math.floor(Math.random()*dsps.length)];
            let header = {
                headers: {
                    // Overwrite Axios's automatically set Content-Type
                    "dsp-code":dsp["dsp-code"],
                    "dsp-token":dsp["dsp-token"],
                    "dsp-secret":dsp["dsp-secret"],
                    'Content-Type': 'application/json'
                }
            }
            axios.post(url, JSON.stringify(transaction), header)
            .then(response => {
                console.log(response.data);
            })
            .catch((error) => {
                console.log(error)
                if (error.response) {
                    console.log(error.response.data);
                    }
            });
            // console.log(`${subtotal_amount}:${total_amount}-${region_code}-${trans_id}`)
            // console.log(header)
        } 


        currentMoment.add(1, 'days');
    }

}