import 'trix';
import 'trix/dist/trix.css';

// L'éditeur n'accepte pas de fichiers : les images passent par les champs dédiés
document.addEventListener('trix-file-accept', (event) => event.preventDefault());

// Alpine est fourni par Livewire : pas besoin de l'importer ici
