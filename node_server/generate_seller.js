function generateUniqueTIN(index) {
    // Generate an 8-digit unique TIN based on the current index
    return String(index * 987654 + 123456).padStart(8, '0');
}

const storeNames = [
    "Chic Charms",
    "Celestial Ceramics",
    "Urban Threads",
    "Epic Electronics",
    "Gourmet Delights",
    // Add more store names as needed
];

const items = [];

for (let i = 1; i <= 300; i++) {
    const storeName = storeNames[Math.floor(Math.random() * storeNames.length)];
    const regionCode = "14"; // You can modify this based on your requirements
    const type = "product"; // or "service" based on your requirements
    const vatType = i % 2 === 0 ? "V" : "NV"; // Alternating V and NV
    const registeredName = `${storeName} Co.`;
    const registeredAddress = i % 2 === 0 ? "555 Elegance Ave, Styleville" : "789 Arts Street, Celestial City";
    const businessName = storeName;
    const tin = generateUniqueTIN(i);
    const contactNumber = `555-${String(i).padStart(4, '0')}`;
    const email = `${storeName.replace(/\s+/g, '').toLowerCase()}@example.com`;
    const sellerType = i % 2 === 0 ? "INDIVIDUAL" : "CORP";

    const item = {
        name: storeName,
        region_code: regionCode,
        type,
        vat_type: vatType,
        registered_name: registeredName,
        registered_address: registeredAddress,
        business_name: businessName,
        tin,
        contact_number: contactNumber,
        email,
        seller_type: sellerType,
    };

    items.push(item);
}

// Output the generated items as JSON
console.log(JSON.stringify(items, null, 2));