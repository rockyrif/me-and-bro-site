/* eslint-disable @typescript-eslint/no-unused-vars */
import React, { type FunctionComponent } from "react";
import { Helmet } from "react-helmet-async";

interface TheHeaderProps {
  title?: string;
}

const TheHeader: FunctionComponent<TheHeaderProps> = ({ title }) => {
  return (
    <div>
      <Helmet defer={false}>
        <title>{title}</title>
        <meta
          name="description"
          content="Discover the latest fashion, accessories, and lifestyle essentials at Me and Bros. Shop now for quality products and unbeatable style."
        />
        <meta
          name="keywords"
          content="Me and Bros, fashion, lifestyle, accessories, online shopping, trendy clothes, e-commerce Sri Lanka"
        />
        <meta name="author" content="Me and Bros Team" />
        <meta name="viewport" content="width=device-width, initial-scale=1" />

        {/* Open Graph / Facebook */}
        <meta property="og:title" content="Me and Bros | Fashion & Lifestyle" />
        <meta
          property="og:description"
          content="Shop the latest fashion and lifestyle essentials at Me and Bros – your go-to online store."
        />
        <meta property="og:type" content="website" />
        <meta property="og:url" content="https://meandbros.lk/" />
        <meta property="og:image" content="https://meandbros.lk/og-image.jpg" />

        {/* Twitter Cards */}
        <meta name="twitter:card" content="summary_large_image" />
        <meta
          name="twitter:title"
          content="Me and Bros | Fashion & Lifestyle"
        />
        <meta
          name="twitter:description"
          content="Shop fashion and lifestyle essentials at Me and Bros – online shopping made easy."
        />
        <meta
          name="twitter:image"
          content="https://meandbros.lk/og-image.jpg"
        />
      </Helmet>
    </div>
  );
};

export default TheHeader;
