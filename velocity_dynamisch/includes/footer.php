    <!-- Footer -->
    <footer>
        <img src="./bilder/sozialemedien.png" alt="Soziale Medien" class="social-media" type="image/png" />
        <img src="./bilder/logo.png" alt="Logo" class="footer-logo" type="image/png" />
        <a href="#top" class="triangle-button" title="Nach Oben"></a>
    </footer>

    <!-- Bootstrap JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" type="application/javascript"></script>

    <!-- Custom JavaScript -->
    <script src="./skripte/script.js" type="application/javascript"></script>

    <script>
        // SortableJS initialisieren
        const sortable = new Sortable(document.getElementById('sortable-cards'), {
            animation: 150, // Animation beim Verschieben
            onEnd: function(evt) {
                // IDs der Fahrräder in der neuen Reihenfolge sammeln
                const cardIds = Array.from(document.querySelectorAll('#sortable-cards .col-md-4')).map(card => card.getAttribute('data-id'));

                // Neue Reihenfolge an das Backend senden
                fetch('update_order.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                        },
                        body: JSON.stringify({
                            order: cardIds
                        }),
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            console.log('Reihenfolge erfolgreich aktualisiert.');
                        } else {
                            console.error('Fehler beim Aktualisieren der Reihenfolge.');
                        }
                    })
                    .catch(error => {
                        console.error('Fehler:', error);
                    });
            },
        });
    </script>
    </body>

    </html>