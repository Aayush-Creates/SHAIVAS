document.getElementById('calculate').addEventListener('click', () => {
    const standCount = parseFloat(document.getElementById('standCount').value);
    const rowLength = parseFloat(document.getElementById('rowLength').value);
    const rowSpacing = parseFloat(document.getElementById('rowSpacing').value);

    if (isNaN(standCount) || isNaN(rowLength) || isNaN(rowSpacing)) {
        alert('Please fill out all fields with valid numbers.');
        return;
    }

    const rowSpacingFeet = rowSpacing / 12; // Convert inches to feet
    const plantsPerAcre = (standCount / rowLength) * (43560 / rowSpacingFeet);

    document.getElementById('population').value = Math.round(plantsPerAcre);
});
