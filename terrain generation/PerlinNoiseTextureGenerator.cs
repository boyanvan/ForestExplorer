using UnityEngine;

public class PerlinNoiseTextureGenerator : MonoBehaviour
{
    [SerializeField] private bool mrusenSeks = false;
    [Space]
    [SerializeField] private float amplitude;
    [SerializeField] private TerrainGenerator terrainGenerator;
    [Space]
    [SerializeField] public NoisemapSettings ns;

    Renderer renderer;
    Texture2D texture;

    private void OnValidate()
    {
        GenerateTexture();

        terrainGenerator.UpdateTerrain(texture, amplitude);
    }

    void Initialize()
    {
        renderer = GetComponent<Renderer>();

        texture = new Texture2D(ns.size.x, ns.size.y);
        texture.name = "noisemap texture";
        texture.filterMode = FilterMode.Point;
    }
    void GenerateTexture()
    {
        Initialize();

        float[,] noisemap = Noise.GenerateNoisemap(ns);

        texture.SetPixels(NoisemapToPixelmap(noisemap));
        texture.Apply();
        renderer.material.mainTexture = texture;
    }

    Color[] NoisemapToPixelmap(float[,] noisemap)
    {
        Color[] colors = new Color[noisemap.GetLength(0) * noisemap.GetLength(1)];

        for (int y = 0; y < noisemap.GetLength(0); y++)
        {
            for (int x = 0; x < noisemap.GetLength(1); x++)
            {
                colors[y * noisemap.GetLength(0) + x] = Color.Lerp(Color.black, Color.white, noisemap[x, y]);
            }
        }

        return colors;
    }
}