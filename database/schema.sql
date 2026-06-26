CREATE TABLE users (
  id          INT AUTO_INCREMENT PRIMARY KEY,
  nama        VARCHAR(100) NOT NULL,
  email       VARCHAR(150) NOT NULL UNIQUE,
  password    VARCHAR(255) NOT NULL,
  role        ENUM('admin','pelanggan') NOT NULL DEFAULT 'pelanggan',
  foto        VARCHAR(255) NULL,
  foto_thumb  VARCHAR(255) NULL,
  created_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE kategori (
  id          INT AUTO_INCREMENT PRIMARY KEY,
  nama        VARCHAR(100) NOT NULL UNIQUE,
  deskripsi   TEXT NULL,
  created_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE produk (
  id           INT AUTO_INCREMENT PRIMARY KEY,
  kategori_id  INT NOT NULL,
  nama         VARCHAR(150) NOT NULL,
  deskripsi    TEXT NULL,
  harga        INT UNSIGNED NOT NULL,
  berat        INT UNSIGNED NOT NULL,            -- gram, > 0
  stok         INT UNSIGNED NOT NULL DEFAULT 0,
  foto         VARCHAR(255) NULL,                -- asli
  foto_resized VARCHAR(255) NULL,
  foto_thumb   VARCHAR(255) NULL,
  foto_width   INT NULL,
  foto_height  INT NULL,
  aktif        TINYINT(1) NOT NULL DEFAULT 1,
  created_at   TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at   TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  CONSTRAINT fk_produk_kategori FOREIGN KEY (kategori_id)
    REFERENCES kategori(id) ON DELETE RESTRICT,
  INDEX idx_produk_kategori (kategori_id),
  INDEX idx_produk_aktif (aktif)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE provinsi (
  id    INT PRIMARY KEY,
  nama  VARCHAR(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE kota (
  id           INT PRIMARY KEY,
  provinsi_id  INT NOT NULL,
  nama         VARCHAR(100) NOT NULL,
  kode_pos     VARCHAR(10) NULL,
  CONSTRAINT fk_kota_provinsi FOREIGN KEY (provinsi_id)
    REFERENCES provinsi(id) ON DELETE CASCADE,
  INDEX idx_kota_provinsi (provinsi_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE kecamatan (
  id       INT PRIMARY KEY,
  kota_id  INT NOT NULL,
  nama     VARCHAR(100) NOT NULL,
  kode_pos VARCHAR(10) NULL,
  CONSTRAINT fk_kecamatan_kota FOREIGN KEY (kota_id)
    REFERENCES kota(id) ON DELETE CASCADE,
  INDEX idx_kecamatan_kota (kota_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE transaksi (
  id              INT AUTO_INCREMENT PRIMARY KEY,
  kode_transaksi  VARCHAR(30) NOT NULL UNIQUE,
  user_id         INT NULL,                      -- NULL = guest
  nama_penerima   VARCHAR(100) NOT NULL,
  kontak          VARCHAR(30) NOT NULL,
  alamat          TEXT NOT NULL,
  destination_id  INT NOT NULL,                  -- id kecamatan (snapshot)
  kurir           VARCHAR(20) NOT NULL,
  layanan         VARCHAR(50) NOT NULL,
  ongkir          INT UNSIGNED NOT NULL,
  etd             VARCHAR(30) NULL,
  berat_total     INT UNSIGNED NOT NULL,
  subtotal        INT UNSIGNED NOT NULL,
  total           INT UNSIGNED NOT NULL,
  status          ENUM('menunggu_pembayaran','dibayar','dikirim','selesai','batal')
                  NOT NULL DEFAULT 'menunggu_pembayaran',
  resi            VARCHAR(50) NULL,
  created_at      TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_transaksi_user FOREIGN KEY (user_id)
    REFERENCES users(id) ON DELETE SET NULL,
  CONSTRAINT fk_transaksi_kecamatan FOREIGN KEY (destination_id)
    REFERENCES kecamatan(id) ON DELETE RESTRICT,
  INDEX idx_transaksi_user (user_id),
  INDEX idx_transaksi_status (status),
  INDEX idx_transaksi_created (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE detail_transaksi (
  id            INT AUTO_INCREMENT PRIMARY KEY,
  transaksi_id  INT NOT NULL,
  produk_id     INT NOT NULL,
  nama_produk   VARCHAR(150) NOT NULL,           -- snapshot
  harga         INT UNSIGNED NOT NULL,           -- snapshot
  jumlah        INT UNSIGNED NOT NULL,
  subtotal      INT UNSIGNED NOT NULL,
  CONSTRAINT fk_detail_transaksi FOREIGN KEY (transaksi_id)
    REFERENCES transaksi(id) ON DELETE CASCADE,
  CONSTRAINT fk_detail_produk FOREIGN KEY (produk_id)
    REFERENCES produk(id) ON DELETE RESTRICT,
  INDEX idx_detail_transaksi (transaksi_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
