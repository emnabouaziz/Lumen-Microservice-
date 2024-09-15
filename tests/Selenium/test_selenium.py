import time
import pytest
from selenium import webdriver
from selenium.webdriver.chrome.service import Service
from selenium.webdriver.chrome.options import Options
from selenium.webdriver.common.by import By

@pytest.fixture(scope="module")
def driver():
    # Setup Chrome options
    chrome_options = Options()
    chrome_options.add_argument("--headless")  # Run in headless mode

    # Initialize WebDriver
    service = Service(executable_path="C:/chromedriver/chromedriver.exe")
    driver = webdriver.Chrome(service=service, options=chrome_options)
    yield driver
    driver.quit()

def test_swagger_documentation(driver):
    # Accéder à la documentation API (Swagger)
    driver.get('http://localhost:8000/api/documentation')

    # Attendre que la page se charge complètement
    time.sleep(3)

    # Vérifier que le titre de la page contient "Swagger"
    assert "Swagger" in driver.title

    # Vérifier que l'élément Swagger UI est présent
    swagger_ui = driver.find_element(By.CSS_SELECTOR, "div#swagger-ui")
    assert swagger_ui is not None

    # Exemple d'interaction : trouver et cliquer sur un bouton "Try it out"
    try_it_out_buttons = driver.find_elements(By.CSS_SELECTOR, "button.try-out__btn")
    if try_it_out_buttons:
        try_it_out_buttons[0].click()
        time.sleep(2)  # Attendre que le bouton soit cliqué et que le formulaire apparaisse

        # Exemple d'interaction : exécuter une requête
        execute_buttons = driver.find_elements(By.CSS_SELECTOR, "button.execute")
        if execute_buttons:
            execute_buttons[0].click()
            time.sleep(2)  # Attendre la réponse de l'API

            # Vérifier que la réponse est affichée
            response_bodies = driver.find_elements(By.CSS_SELECTOR, "div.responses-inner div.response")
            assert len(response_bodies) > 0

    # Afficher un message de réussite
    print("Test fonctionnel réussi : la documentation Swagger est accessible et l'interaction de base fonctionne.")
