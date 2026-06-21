import nextVitals from "eslint-config-next/core-web-vitals";
import nextTypescript from "eslint-config-next/typescript";

const eslintConfig = [
  ...nextVitals,
  ...nextTypescript,
  {
    ignores: [
      ".next/**",
      "node_modules/**",
      "_reports/**",
      "_proof/**",
      "_deploy/**",
      "_hostinger_build/**",
      "collected/**",
      "experiments/**",
      "routes/**",
      "runtime/**",
      "sites/**",
      "source_review/**",
      "tests/**"
    ],
  },
];

export default eslintConfig;
