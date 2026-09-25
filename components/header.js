class MyHeader extends HTMLElement {
    connectedCallback() {
        const isLoggedIn = this.hasAttribute('data-logged-in') && this.getAttribute('data-logged-in') === 'true';

        this.innerHTML = `
            <header class="header">
                <a class="header-title" href="/index.php">
                    <p>CarSale</p>
                </a>

                <form class="header-search" action="/index.php" method="GET">
                    <input type="text" name="search" placeholder="Поиск по марке или модели">
                </form>

                <div class="header-menu">
                    ${isLoggedIn ? `
                        <a href="/layout/chat.php"><button class="btn-chat">Чат</button></a>
                        <a href="/layout/add_listing.php"><button class="btn-accent">Разместить объявление</button></a>
                        <a href="/layout/profile.php"><button class="btn-user"><img src="/src/icons/user.png" alt=""></button></a>
                        <a href="/scripts/logout.php"><button class="btn-logout"><img src="/src/icons/log-out.png" alt=""></button></a>
                    ` : `
                        <a href="/layout/login.php"><button>Вход</button></a>
                    `}
                </div>
            </header>`;
    }
}
customElements.define('my-header', MyHeader);