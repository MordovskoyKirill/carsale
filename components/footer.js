class MyFooter extends HTMLElement {
    connectedCallback() {
        this.innerHTML = `
            <footer class="footer">
                <div class="footer-inner">
                    <div class="footer-col">
                        <h3>Контакты</h3>
                        <p>Мордовской Кирилл</p>
                        <p>ИСИП-23/1</p>
                    </div>
                    <div class="footer-col">
                        <h3>Ссылки</h3>
                        <a href="/index.php">Главная</a>
                        <a href="/index.php#catalog">Каталог</a>
                        <a href="/layout/add_listing.php">Разместить объявление</a>
                    </div>
                    <div class="footer-col">
                        <p class="footer-copyright">© 2026 CarSale. Все права защищены.</p>
                    </div>
                    <div class="footer-col">
                        <img src="/src/icons/logo.png" alt="">
                        <p class="footer-title">CarSale</p>
                    </div>
                </div>
            </footer>`;
    }
}
customElements.define('my-footer', MyFooter);