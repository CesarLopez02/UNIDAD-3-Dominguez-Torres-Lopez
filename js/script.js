 // Clase para manejar las transiciones de opacidad
        class TransitionBox {
            constructor(elementId) {
                this.element = document.getElementById(elementId);
            }

            fadeIn() {
                this.element.style.transition = 'opacity 0.5s ease-in-out';
                this.element.style.opacity = '1';
            }

            fadeOut() {
                this.element.style.transition = 'opacity 0.5s ease-in-out';
                this.element.style.opacity = '0';
            }
        }

        // Crear una instancia de TransitionBox para el section con ID product-section
        const productSection = new TransitionBox('product-section');

        // Ejecutar fadeOut al cargar la página
        document.addEventListener('DOMContentLoaded', () => {
            productSection.fadeOut();

            // Luego de 2 segundos, ejecutar fadeIn para mostrar el section
            setTimeout(() => {
                productSection.fadeIn();
            }, 2000);
        });

        // Clase para manejar la animación del texto del encabezado
        class AnimatedHeader {
            constructor(textIds) {
                this.textElements = textIds.map(id => document.getElementById(id));
                this.addEventListeners();
            }

            addEventListeners() {
                this.textElements.forEach(textElement => {
                    textElement.addEventListener('mouseover', () => {
                        this.animateText(textElement);
                    });
                    textElement.addEventListener('mouseout', () => {
                        this.resetText(textElement);
                    });
                });
            }

            animateText(element) {
                element.style.color = 'red'; // Cambia el color al pasar el cursor
            }

            resetText(element) {
                element.style.color = ''; // Restaura el color original
            }
        }

        // Crear una instancia de AnimatedHeader para los textos del encabezado
        const animatedHeader = new AnimatedHeader(['text1', 'text2', 'text3']);

        // Definición de clase para producto
        class Product {
            constructor(id, name, imageUrl, price, sale = false, salePrice = 0) {
                this.id = id;
                this.name = name;
                this.imageUrl = imageUrl;
                this.price = price;
                this.sale = sale;
                this.salePrice = salePrice;
            }

            render() {
                let card = document.createElement('div');
                card.classList.add('col', 'mb-5', 'card');
                card.innerHTML = `
                    <div class="card h-100">
                        ${this.sale ? '<div class="badge bg-dark text-white position-absolute" style="top: 0.5rem; right: 0.5rem">Sale</div>' : ''}
                        <img class="card-img-top" src="${this.imageUrl}" alt="..." />
                        <div class="card-body p-4">
                            <div class="text-center">
                                <h5 class="fw-bolder">${this.name}</h5>
                                ${this.renderStars()}
                                ${this.sale ? `<span class="text-muted text-decoration-line-through">$${this.price}</span>` : ''}
                                $${this.sale ? this.salePrice : this.price}
                            </div>
                        </div>
                        <div class="card-footer p-4 pt-0 border-top-0 bg-transparent">
                            <div class="text-center"><a class="btn btn-outline-dark mt-auto" href="#" onclick="addToCart(${this.id})">Add to cart</a></div>
                        </div>
                    </div>
                `;
                return card;
            }

            renderStars() {
                return '<div class="d-flex justify-content-center small text-warning mb-2"><div class="bi-star-fill"></div><div class="bi-star-fill"></div><div class="bi-star-fill"></div></div>';
            }
        }

        // Función Asincrona para añadir al carrito con llamada AJAX
        async function addToCartAsync(productId) {
            try {
                const response = await fetch('https://jsonplaceholder.typicode.com/posts', {
                    method: 'POST',
                    body: JSON.stringify({
                        productId: productId
                    }),
                    headers: {
                        'Content-type': 'application/json; charset=UTF-8',
                    },
                });
                const data = await response.json();

                let cartCount = parseInt(document.getElementById('cart-count').innerText);
                cartCount++;
                document.getElementById('cart-count').innerText = cartCount;
                Swal.fire({
                    icon: 'success',
                    title: 'Producto añadido al carrito',
                    showConfirmButton: false,
                    timer: 1500
                });
            } catch (error) {
                console.error('Error al añadir al carrito:', error);
            }
        }
        function addToCart(productId) {
            addToCartAsync(productId);
        }

        // Datos de productos y funcion Sincrona
        const products = [
            new Product(1, 'Perfect Sense', 'https://i5.walmartimages.com.mx/mg/gm/3pp/asr/b2f96798-b43d-4419-acac-c21311eb8987.3b99d0cb8d4ad11e3cd36370a138a827.jpeg?odnHeight=612&odnWidth=612&odnBg=FFFFFF', 40, false),
            new Product(2, 'Perfect Sense', 'https://i5.walmartimages.com.mx/mg/gm/3pp/asr/e896395b-555f-4c17-84ed-9a665a5f4deb.1fef573418b1053bf5ed76f6489366d1.png?odnHeight=612&odnWidth=612&odnBg=FFFFFF', 18, true, 20),
            new Product(3, 'Perfect Sense', 'https://i5.walmartimages.com.mx/mg/gm/3pp/asr/c8c39c45-0f4c-4c4c-bf8c-31cf7488ba80.62cff588bffa86c442febb854ee56c35.png?odnHeight=612&odnWidth=612&odnBg=FFFFFF', 25, true, 50),
            new Product(4, 'Perfect Sense', 'https://i5.walmartimages.com.mx/mg/gm/3pp/asr/1691761b-715b-49ef-9e56-814324ffa345.963d8e4a5673f3f3b0aca9fe87a023f2.png?odnHeight=612&odnWidth=612&odnBg=FFFFFF', 40, false),
            new Product(5, 'Ganador', 'https://i5.walmartimages.com.mx/mg/gm/1p/images/product-images/img_large/00750200287277l.jpg?odnHeight=612&odnWidth=612&odnBg=FFFFFF', 120),
            new Product(6, 'Pedigree', 'https://i5.walmartimages.com.mx/mg/gm/1p/images/product-images/img_large/00070646002387l.jpg?odnHeight=612&odnWidth=612&odnBg=FFFFFF', 25, true, 50),
            new Product(7, 'Ganador Premium', 'https://i5.walmartimages.com.mx/mg/gm/1p/images/product-images/img_large/00750200287128l.jpg?odnHeight=612&odnWidth=612&odnBg=FFFFFF', 18, true, 20),
            new Product(8, 'Nucan', 'https://i5.walmartimages.com.mx/mg/gm/3pp/asr/449165b0-54e6-41bf-b1da-a6785402f49b.0afda167ac723721d94133088f4b171b.png?odnHeight=612&odnWidth=612&odnBg=FFFFFF', 40)
        ];

        // Renderizar los productos en el DOM
        const productContainer = document.getElementById('product-list');
        products.forEach(product => {
            let productCard = product.render();
            productContainer.appendChild(productCard);
        });

        // Evento de mouseover para resaltar las tarjetas al pasar el ratón
        document.querySelectorAll('.card').forEach(card => {
            card.addEventListener('mouseover', () => {
                card.classList.add('bg-light');
            });
            card.addEventListener('mouseout', () => {
                card.classList.remove('bg-light');
            });
        });