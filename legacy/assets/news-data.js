// Noticias de respaldo: se muestran cuando Supabase aún no está configurado
// (ver assets/supabase-config.js) o mientras no haya noticias cargadas.
export const FALLBACK_NEWS = [
  {
    id: "sample-1",
    title: "Aproavena en Campo Sureño: la industria de la avena sigue creciendo",
    summary: "El diario Campo Sureño reseñó el desarrollo del sector avenero nacional y el rol de los procesadores asociados a Aproavena en la cadena de valor.",
    body: "Campo Sureño publicó una nota sobre el estado de la industria procesadora de avena en Chile, destacando el trabajo conjunto de los asociados a Aproavena en el mejoramiento de estándares de calidad e inocuidad. Puedes revisar la publicación original en el enlace descargable de esta noticia.",
    cover_url: "./assets/foto-campo-cielo.webp",
    published_at: "2022-04-18",
    pdf_url: "./assets/noticia-campo-sureno-2022.pdf"
  },
  {
    id: "sample-2",
    title: "Socios de Aproavena se reúnen para revisar la temporada de siembra",
    summary: "Representantes de las empresas asociadas analizaron proyecciones de superficie sembrada y condiciones agroclimáticas para la próxima temporada.",
    body: "En su reunión periódica, los asociados de Aproavena revisaron las proyecciones de superficie sembrada de avena para la temporada, junto con las condiciones agroclimáticas de las principales zonas productoras del sur de Chile. La asociación continúa trabajando en conjunto con productores para propiciar el cultivo de avena en todo el país.",
    cover_url: "./assets/foto-campo-agricultor.webp",
    published_at: "2024-09-03",
    pdf_url: null
  },
  {
    id: "sample-3",
    title: "Avanza el trabajo por la calidad e inocuidad de la avena nacional",
    summary: "Aproavena impulsa iniciativas conjuntas entre sus asociados para fortalecer los atributos de calidad de los productos elaborados en base a avena.",
    body: "Como parte de sus objetivos permanentes, Aproavena continúa fortaleciendo y potenciando los atributos de calidad e inocuidad de los productos elaborados a partir de avena nacional, en coordinación con sus empresas asociadas y organismos públicos y privados vinculados a la actividad.",
    cover_url: "./assets/foto-procesamiento-grano.webp",
    published_at: "2025-01-20",
    pdf_url: null
  }
];
