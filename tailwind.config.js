module.exports = {
  content: ["./templates/**/*.twig", "./assets/**/*.js"],
  theme: { extend: {} },
  plugins: [require('@tailwindcss/typography')],
};
