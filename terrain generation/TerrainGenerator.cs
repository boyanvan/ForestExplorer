using UnityEngine;

public class TerrainGenerator : MonoBehaviour
{
    Terrain terrain;
    TerrainData tData;

    void Initialize()
    {
        terrain = GetComponent<Terrain>();
        tData = terrain.terrainData;
    }
    public void UpdateTerrain(Texture2D noisemapTexture, float amplitude)
    {
        Initialize();

        tData.SetHeights(0, 0, GetHeightmapFromTexture(noisemapTexture, amplitude));
    }

    float[,] GetHeightmapFromTexture(Texture2D texture, float amplitude)
    {
        int width = texture.width;
        int height = texture.height;

        float[,] heightmap = new float[width, height];

        for (int y = 0; y < height; y++)
        {
            for (int x = 0; x < width; x++)
            {
                Color pixel = texture.GetPixel(x, y);

                float value = (pixel.r + pixel.g + pixel.b) / 3f;

                heightmap[x, y] = value * amplitude;
            }
        }

        return heightmap;
    }
}