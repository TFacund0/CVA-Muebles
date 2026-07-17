import type { NextConfig } from "next";

const nextConfig: NextConfig = {
  images: {
    // Las imágenes de productos/galería se suben a Cloudinary desde el backend (CloudinaryService).
    remotePatterns: [
      {
        protocol: "https",
        hostname: "res.cloudinary.com",
      },
    ],
  },
};

export default nextConfig;
